<?php

namespace app\models;

use app\models\query\ArticleQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UnauthorizedHttpException;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property string $body
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property ArticleTag[] $articleTags
 * @property Tag[] $tags
 * @property Comment[] $comments
 * @property Favourite[] $favourites
 * @property boolean $favorited
 */
class Article extends ActiveRecord
{
    const SCENARIO_UPDATE = 'update';

    private $_favoritesCount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'description', 'body'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'title',
                'description',
                'body',
            ], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['body'], 'string'],
            [['title', 'description'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['slug', 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'slug' => 'Slug',
            'title' => 'Title',
            'description' => 'Description',
            'body' => 'Body',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
            ],
            [
                'class' => BlameableBehavior::class,
                'updatedByAttribute' => false,
                'createdByAttribute' => 'user_id',
            ],
            [
                'class' => TimestampBehavior::class,
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::class, ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('article_tag', ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavourites()
    {
        return $this->hasMany(Favourite::className(), ['article_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function getFavorited() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return $this->getFavourites()->andWhere(['user_id' => Yii::$app->user->id])->exists();
    }

    public function setFavouritesCount($value) {
        $this->_favoritesCount = intval($value);
    }

    public function getFavouritesCount() {
        return $this->_favoritesCount;
    }

    public static function find()
    {
        return (new ArticleQuery(get_called_class()))->withFavouritesCount();
    }

    public static function findBySlug($slug) {
        return self::find()->andWhere(['slug' => $slug])->one();
    }

    /**
     * @param Comment $comment
     *
     * @return Comment
     */
    public function addComment($comment) {
        $comment->article_id = $this->id;
        $comment->save();
        return $comment;
    }

    /**
     * @param null $userId
     *
     * @return bool
     * @throws UnauthorizedHttpException
     */
    public function setFavourite($userId = null) {
        $userId = $userId ?: Yii::$app->user->id;

        if (is_null($userId)) {
            throw new UnauthorizedHttpException('You must login to set favourite');
        }

        $favourite = new Favourite([
            'user_id' => $userId,
            'article_id' => $this->id,
        ]);

        return $favourite->save();
    }

    /**
     * @param null $userId
     *
     * @throws UnauthorizedHttpException
     */
    public function deleteFavourite($userId = null) {
        $userId = $userId ?: Yii::$app->user->id;

        if (is_null($userId)) {
            throw new UnauthorizedHttpException('You must login to set favourite');
        }

        Favourite::deleteAll(['article_id' => $this->id, 'user_id' => $userId]);
    }
}

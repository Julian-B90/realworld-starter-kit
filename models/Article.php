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
 * @property Favorite[] $favorites
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
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
                'ensureUnique' => true
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
        return $this->hasMany(Comment::class, ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorite::class, ['article_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function getFavorited() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return $this->getFavorites()->where(['user_id' => Yii::$app->user->id])->exists();
    }

    public function setFavoritesCount($value) {
        $this->_favoritesCount = intval($value);
    }

    public function getFavoritesCount() {
        $count = !is_null($this->_favoritesCount)
            ? $this->_favoritesCount
            : $this->getFavorites()->count();
        return intval($count);
    }

    public static function find()
    {
        return (new ArticleQuery(get_called_class()))->withFavoritesCount();
    }

    public static function findBySlug($slug) {
        return self::find()->where(['slug' => $slug])->one();
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
    public function setFavorite($userId = null) {
        $userId = $userId ?: Yii::$app->user->id;

        if (is_null($userId)) {
            throw new UnauthorizedHttpException('You must login to set favorite');
        }

        $favorite = new Favorite([
            'user_id' => $userId,
            'article_id' => $this->id,
        ]);

        return $favorite->save();
    }

    /**
     * @param null $userId
     *
     * @throws UnauthorizedHttpException
     */
    public function deleteFavorite($userId = null) {
        $userId = $userId ?: Yii::$app->user->id;

        if (is_null($userId)) {
            throw new UnauthorizedHttpException('You must login to set favorite');
        }

        Favorite::deleteAll(['article_id' => $this->id, 'user_id' => $userId]);
    }
}

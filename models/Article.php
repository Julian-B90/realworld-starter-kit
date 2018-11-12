<?php

namespace app\models;

use app\models\query\ArticleQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
    private $_favoritesCount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
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
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('article_tag', ['article_id' => 'id']);
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
        $this->_favoritesCount = $value;
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
}

<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "follow".
 *
 * @property int $id
 * @property int $follower_id
 * @property int $followed_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $followed
 * @property User $follower
 */
class Follow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'follow';
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            ['class' => TimestampBehavior::class,]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['follower_id', 'followed_id'], 'required'],
            [['follower_id', 'followed_id', 'created_at', 'updated_at'], 'integer'],
            [['followed_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['followed_id' => 'id']],
            [['follower_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['follower_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'follower_id' => 'Follower ID',
            'followed_id' => 'Followed ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowed()
    {
        return $this->hasOne(User::className(), ['id' => 'followed_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(User::className(), ['id' => 'follower_id']);
    }
}

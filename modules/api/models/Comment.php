<?php


namespace app\modules\api\models;

/**
 * Class Comment
 *
 * @package app\modules\api\models
 * @property Profile $profile
 */
class Comment extends \app\models\Comment
{
    public function fields()
    {
        return [
            'id',
            'createdAt' => function (self $model) {
                return \Yii::$app->formatter->asDatetime($model->created_at);
            },
            'updatedAt' => function (self $model) {
                return \Yii::$app->formatter->asDatetime($model->updated_at);
            },
            'body',
            'author' => function (self $model) {
                return $model->profile;
            }
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['id' => 'user_id']);
    }

    public function formName()
    {
        return 'comment';
    }
}
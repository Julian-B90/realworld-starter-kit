<?php


namespace app\modules\api\models;


use app\models\User;

class Profile extends User
{
    public $following = false;

    public function fields()
    {
        return [
            'username',
            'bio',
            'image',
            'following'
        ];
    }

    public static function find()
    {
        $query = parent::find();
        if (!\Yii::$app->user->isGuest) {
            $query = $query->withFollowing(\Yii::$app->user->id);
        }
        return $query;
    }
}
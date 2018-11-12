<?php


namespace app\modules\api\models;


class User extends \app\models\User
{
    public function fields()
    {
        return [
            'email',
            'token',
            'username',
            'bio',
            'image',
        ];
    }
}
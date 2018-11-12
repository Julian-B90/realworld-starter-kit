<?php


namespace app\modules\api\models;


use yii\base\Model;

class LoginUserParams extends Model
{
    public $email;
    public $password;

    public function rules()
    {
        return [
          [['email', 'password'], 'required'],
          [['email', 'password'], 'string'],
          ['email', 'email'],
        ];
    }

    public function formName()
    {
        return 'user';
    }
}
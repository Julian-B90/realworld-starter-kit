<?php


namespace app\modules\api\models;


use yii\base\Model;

class RegisterUserParams extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules()
    {
        return [
          [['email', 'password', 'username'], 'required'],
          [['email', 'password', 'username'], 'string'],
          ['email', 'email'],
        ];
    }

    public function formName()
    {
        return 'user';
    }
}
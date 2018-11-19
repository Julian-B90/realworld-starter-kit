<?php


namespace app\tests\fixtures\models;


use app\models\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
    public $dataFile = '@app/tests/fixtures/data/user.php';
}
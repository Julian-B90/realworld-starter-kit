<?php


namespace app\tests\fixtures\models;


use app\models\Favorite;
use yii\test\ActiveFixture;

class FavoriteFixture extends ActiveFixture
{
    public $modelClass = Favorite::class;
    public $depends = [UserFixture::class, ArticleFixture::class];
    public $dataFile = '@app/tests/fixtures/data/favorite.php';

}
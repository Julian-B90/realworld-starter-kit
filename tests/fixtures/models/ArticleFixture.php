<?php


namespace app\tests\fixtures\models;


use app\models\Article;
use yii\test\ActiveFixture;

class ArticleFixture extends ActiveFixture
{
    public $modelClass = Article::class;
    public $depends = [UserFixture::class];
    public $dataFile = '@app/tests/fixtures/data/article.php';
}
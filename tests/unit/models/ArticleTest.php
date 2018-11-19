<?php

use app\models\Article;
use app\models\Comment;
use app\models\User;
use app\tests\fixtures\models\ArticleFixture;
use app\tests\fixtures\models\FavoriteFixture;
use app\tests\fixtures\models\UserFixture;

class ArticleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        $user = $this->tester->grabFixture('users', 'user1');
        Yii::$app->user->login($user);
    }

    protected function _after()
    {
    }

    public function _fixtures() {
        return [
            'articles' => [
                'class' => ArticleFixture::class
            ],
            'users' => [
                'class' => UserFixture::class,
            ],
            'favorites' => [
                'class' => FavoriteFixture::class,
            ]
        ];
    }

    // tests
    public function testCreate()
    {
        $model = new Article();
        $this->tester->assertFalse($model->validate(), 'Model validation failed');
        $this->tester->assertArrayHasKey('title', $model->errors, 'Title required');
        $this->tester->assertArrayHasKey('body', $model->errors, 'Body required');
        $this->tester->assertArrayHasKey('description', $model->errors, 'Description required');

        $model->body = 'Test body';
        $model->title = 'Test title';
        $model->description = 'Test description';

        $this->tester->assertTrue($model->save(), 'Model save successfully');

        $this->tester->seeRecord(Article::class, ['id' => $model->id], 'Model exists in DB');
    }

    public function testFavoritesCount() {
        /** @var Article $article */
        $article = $this->tester->grabFixture('articles', 'article1');
        $this->tester->assertEquals(1, $article->getFavoritesCount(),
            'Favorites count is correct');
    }

    public function testAddComment() {
        /** @var Article $article */
        $article = $this->tester->grabFixture('articles', 'article1');
        $article->addComment(new Comment([
            'body' => 'Test comment'
        ]));
        $this->tester->assertEquals(1, count($article->comments));
    }

    /**
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function testFavorite() {
        /** @var Article $article */
        $article = $this->tester->grabFixture('articles', 'article1');
        $article->deleteFavorite();
        $this->tester->assertEquals(0, $article->getFavorites()->count(), 'Delete favorite succeed');
        $article->setFavorite();
        $this->tester->assertEquals(1, $article->getFavorites()->count(), 'Set favorite succeed');
    }
}
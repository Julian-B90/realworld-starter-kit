<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Article;
use app\modules\api\models\Comment;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class CommentController extends CommonController
{
    public $root = 'comment';

    /**
     * @param $slug
     *
     * @return Comment
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionCreate($slug) {
        /** @var Article $model */
        $model = $this->findArticleModel($slug);

        $comment = new Comment();
        $comment->load(\Yii::$app->request->getBodyParams());
        $model->addComment($comment);
        return $comment;
    }

    /**
     * @param $slug
     *
     * @return \app\models\Comment[]
     * @throws NotFoundHttpException
     */
    public function actionIndex($slug) {
        $model = $this->findArticleModel($slug);
        return $model->comments;
    }

    /**
     * @param $slug
     * @param $id
     *
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($slug, $id) {
        $article = $this->findArticleModel($slug);
        if ($article->user_id != \Yii::$app->user->id) {
            throw new UnauthorizedHttpException('You can\'t delete this comment');
        }

        $comment = Comment::findOne($id);
        if (!is_null($comment)) {
            $comment->delete();
        }
    }

    /**
     * @param $slug
     *
     * @return array|null|Article
     * @throws NotFoundHttpException
     */
    public function findArticleModel($slug) {
        $model = Article::findBySlug($slug);
        if(is_null($model)) {
            throw new NotFoundHttpException('Article not found by slug');
        }
        return $model;
    }
}
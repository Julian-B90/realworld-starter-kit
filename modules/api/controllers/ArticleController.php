<?php


namespace app\modules\api\controllers;

use app\models\ArticleTag;
use app\models\Tag;
use app\modules\api\models\Article;
use app\modules\api\models\ArticleFeedSearch;
use app\modules\api\models\ArticleSearch;
use app\modules\api\models\Comment;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class ArticleController extends CommonController
{
    public $root = 'article';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = [
            'index',
            'view',
        ];
        return $behaviors;
    }

    /**
     * @param $slug
     *
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($slug)
    {
        return $this->findModel($slug);
    }

    /**
     * @param $slug
     *
     * @return Article
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($slug) {
        /* @var $model Article */
        $model = $this->findModel($slug);
        $model->setScenario(Article::SCENARIO_UPDATE);

        $model->load(\Yii::$app->getRequest()->getBodyParams());
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param $slug
     *
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($slug) {
        $model = $this->findModel($slug);

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
    }

    /**
     * @return Article
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException|Exception
     */
    public function actionCreate() {
        $model = new Article();
        $params = \Yii::$app->getRequest()->getBodyParams();

        $model->load($params, $this->root);

        $transaction = \Yii::$app->db->beginTransaction();
        if ($model->save()) {
            $tagList = $params[$this->root]['tagList'] ?? null;

            if (!is_null($tagList)) {
                ArticleTag::deleteAll(['article_id' => $model->id]);

                foreach ($tagList as $tagItem) {
                    $tag = Tag::findOne(['name' => $tagItem]);
                    // Create new tag if not exist
                    if (is_null($tag)) {
                        $tag = new Tag([
                            'name' => $tagItem
                        ]);
                        if (!$tag->save()) {
                            throw new InvalidArgumentException('Error saving new tag');
                        }
                    }

                    $articleTag = new ArticleTag([
                        'article_id' => $model->id,
                        'tag_id' => $tag->id,
                    ]);
                    if (!$articleTag->save()) {
                        throw new InvalidConfigException('Error saving article_tag');
                    }
                }
            }

            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'slug' => $model->slug], true));
        } elseif (!$model->hasErrors()) {
            $transaction->rollBack();
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        $transaction->commit();

        return $model;
    }

    /**
     * @return Article[]
     */
    public function actionIndex() {
        $searchModel = new ArticleSearch();
        return $searchModel->search(\Yii::$app->request->getQueryParams());
    }

    public function actionFeed() {
        $searchModel = new ArticleFeedSearch();
        return $searchModel->search(\Yii::$app->request->getQueryParams());
    }

    /**
     * @param $slug
     *
     * @throws NotFoundHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function actionFavourite($slug) {
        $model = $this->findModel($slug);
        $model->setFavourite();
        return $model;
    }

    /**
     * @param $slug
     *
     * @throws NotFoundHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function actionDeleteFavourite($slug) {
        $model = $this->findModel($slug);
        $model->deleteFavourite();
        return $model;
    }

    /**
     * @param $slug
     *
     * @return array|null|Article
     * @throws NotFoundHttpException
     */
    public function findModel($slug) {
        $model = Article::findBySlug($slug);
        if(is_null($model)) {
            throw new NotFoundHttpException('Article not found by slug');
        }
        return $model;
    }
}
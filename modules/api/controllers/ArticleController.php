<?php


namespace app\modules\api\controllers;

use app\modules\api\models\Article;
use yii\helpers\Url;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class ArticleController extends CommonController
{
    public $root = 'article';
    public $modelClass = Article::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'],
            $actions['update'],
            $actions['delete'],
            $actions['create']
        );
        return $actions;
    }

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

        $model->load(\Yii::$app->getRequest()->getBodyParams(), $this->root);
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
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate() {
        $model = new Article();

        $model->load(\Yii::$app->getRequest()->getBodyParams(), $this->root);
        if ($model->save()) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'slug' => $model->slug], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param $slug
     *
     * @return array|null|\yii\db\ActiveRecord
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
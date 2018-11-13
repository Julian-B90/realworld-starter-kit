<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Profile;
use app\modules\api\models\User;
use yii\web\NotFoundHttpException;

class ProfileController extends CommonController
{

    public function actions()
    {
        return [];
    }

    /**
     * @param $username
     *
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($username)
    {
        return $this->findModel($username);
    }

    /**
     * @param $username
     *
     * @throws NotFoundHttpException
     */
    public function actionFollow($username) {
        $user = $this->findModel($username);
        /** @var User $currentUser */
        $currentUser = \Yii::$app->user->identity;
        $currentUser->follow($user);
        return $this->findModel($username);
    }

    /**
     * @param $username
     *
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUnfollow($username) {
        $user = $this->findModel($username);
        /** @var User $currentUser */
        $currentUser = \Yii::$app->user->identity;
        $currentUser->unfollow($user);
        return $this->findModel($username);
    }

    /**
     * @param $username
     *
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($username) {
        $model = Profile::findByUsername($username);
        if(is_null($model)) {
            throw new NotFoundHttpException('Article not found by slug');
        }
        return $model;
    }
}
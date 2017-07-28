<?php

namespace app\controllers;

use yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\User;
use yii\rest\Controller;

class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        $behaviors['tokenAuth'] = [
            'class' => \app\auth\HttpTokenAuth::className(),
            'except' => ['options', 'login', 'create']
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        return ['user' => Yii::$app->user->identity];
    }

    public function actionCreate()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new User();

        $model->load(Yii::$app->getRequest()->getBodyParams(), 'user');
        $user = $model->findByEmail($model->email);

        if ($user === null) {
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
            return ['user' => $model];
        }

        return ['user' => $user];
    }

    public function actionUpdate()
    {
        $userData = Yii::$app->request->post('user');

        $model = User::findOne(['email' => $userData['email']]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), 'user');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return ['user' => $model];
    }

    public function actionLogin()
    {
        $userData = Yii::$app->request->post('user');

        $user = User::findOne([
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $user->token = Yii::$app->security->generateRandomString();
        $user->save();

        return ['user' => $user];
    }
}

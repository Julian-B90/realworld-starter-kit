<?php

namespace app\controllers;

use yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use app\models\User;

class ProfileController extends Controller
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
            'except' => ['options']
        ];

        return $behaviors;
    }

    public function actionView($username)
    {
        $profile = User::findByUsername($username);

        if ($profile === null) {
            throw new NotFoundHttpException();
        }

        return [
            'profile' => [
                'username' => $profile->username,
                'bio' => $profile->bio,
                'image' => $profile->image,
                'following' =>  false
            ]
        ];
    }
}

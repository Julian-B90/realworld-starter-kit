<?php

namespace app\controllers;

use yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\Follow;

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

    private function _loadProfile($username)
    {
        $profile = User::findByUsername($username);

        if ($profile === null) {
            throw new NotFoundHttpException();
        }

        return $profile;
    }

    public function actionView($username)
    {
        $profile = $this->_loadProfile($username);

        return [
            'profile' => [
                'username' => $profile->username,
                'bio' => $profile->bio,
                'image' => $profile->image,
                'following' =>  false
            ]
        ];
    }

    public function actionFollow($username)
    {
        $profile = $this->_loadProfile($username);

        $follow = new Follow();
        $follow->user_id = Yii::$app->user->id;
        $follow->follow_id = $profile->id;

        if ($follow->save()) {
            return [
                'profile' => [
                    'username' => $profile->username,
                    'bio' => $profile->bio,
                    'image' => $profile->image,
                    'following' => true,
                ]
            ];
        }

        return $follow;
    }

    public function actionUnFollow($username)
    {
        $profile = $this->_loadProfile($username);

        $follow = Follow::findOne([
            'user_id' => Yii::$app->user->id,
            'follow_id' => $profile->id,
        ]);

        if ($follow->delete()) {
            return [
                'profile' => [
                    'username' => $profile->username,
                    'bio' => $profile->bio,
                    'image' => $profile->image,
                    'following' => false,
                ]
            ];
        }

        return $follow;
    }
}

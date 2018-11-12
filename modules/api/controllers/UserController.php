<?php

namespace app\modules\api\controllers;

use app\modules\api\models\LoginUserParams;
use app\modules\api\models\RegisterUserParams;
use app\modules\api\models\User;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\web\HttpException;

class UserController extends CommonController
{
    public $root = 'user';

    public $modelClass = User::class;

    public function actions()
    {
        $actions =  parent::actions();
        unset(
            $actions['index'],
            $actions['delete'],
            $actions['create'],
            $actions['view']
        );
        return $actions;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[self::AUTHENTICATOR_BEHAVIOR]['optional'] = [
            'login',
            'create',
        ];
        return $behaviors;
    }

    /**
     * @return User|LoginUserParams
     * @throws HttpException|InvalidConfigException
     */
    public function actionLogin() {
        $params = new LoginUserParams();
        $params->load(\Yii::$app->request->getBodyParams());
        if (!$params->validate()) {
            return $params;
        }

        $user = User::findByEmail($params->email);
        $user->setScenario(User::SCENARIO_LOGIN);

        if (is_null($user) || !$user->validatePassword($params->password)) {
            throw new HttpException(422, 'Email or password are incorrect');
        }

        $user->login();
        $user->refresh();

        return $user;
    }

    /**
     * @return RegisterUserParams|User
     * @throws InvalidConfigException
     */
    public function actionCreate() {
        $params = new RegisterUserParams();
        $params->load(\Yii::$app->request->getBodyParams());
        if (!$params->validate()) {
            return $params;
        }

        $user = new User([
            'username' => $params->username,
            'password' => $params->password,
            'email' => $params->email,
        ]);
        $user->setScenario(User::SCENARIO_REGISTER);

        if($user->register()) {
           $user->refresh();
        }
        return $user;
    }

    public function actionView() {
        return \Yii::$app->user->identity;
    }
}
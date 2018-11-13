<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Tag;

class TagController extends CommonController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors[self::AUTHENTICATOR_BEHAVIOR]['optional'] = [
            'index'
        ];
        return $behaviors;
    }

    public $root = 'tag';

    public function actionIndex() {
        return Tag::find()->select('name')->column();
    }
}
<?php

namespace app\components;

use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{
    protected function renderException($exception)
    {
        $response = \Yii::$app->response;
        if (!is_null($this->errorAction)) {
            $result = \Yii::$app->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        }
        $response->setStatusCodeByException($exception);
        $response->send();
    }
}
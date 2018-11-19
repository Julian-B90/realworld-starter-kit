<?php


namespace app\controllers;


use yii\base\Exception;
use yii\web\Controller;

class ErrorController extends Controller
{
    public function actionIndex() {
        $exception = \Yii::$app->errorHandler->exception;
        $message = 'Unknown error occured';
        if ($exception !== null) {
            $message = $exception->getMessage();
        }

        return [
            'errors' => [
                'body' => [
                    $message
                ]
            ]
        ];
    }
}
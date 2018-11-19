<?php

namespace app\modules\api\components;

class Serializer extends \yii\rest\Serializer
{
    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed.');

        return $model->getErrors();
    }
}
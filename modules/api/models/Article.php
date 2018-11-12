<?php


namespace app\modules\api\models;


use yii\helpers\ArrayHelper;

class Article extends \app\models\Article
{

    public function fields()
    {
        return [
            'slug',
            'title',
            'description',
            'body',
            'tagList' => function (Article $model) {
                return ArrayHelper::getColumn($model->tags, 'name');
            },
            'createdAt' => function (Article $model) {
                return \Yii::$app->formatter->asDatetime($model->created_at);
            },
            'updatedAt' => function (Article $model) {
                return \Yii::$app->formatter->asDatetime($model->updated_at);
            },
            'favorited',
            'favouritesCount',
            'author',
        ];
    }

    public function getAuthor() {
        return $this->hasOne(Profile::class, ['id' => 'user_id']);
    }
}
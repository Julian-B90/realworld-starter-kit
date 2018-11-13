<?php


namespace app\modules\api\models;


use app\models\Favourite;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class ArticleFeedSearch extends Article
{
    public $limit;
    public $offset;

    public function rules()
    {
        return [
            [['limit', 'offset'], 'integer'],
            ['limit', 'default', 'value' => 20],
            ['offset', 'default', 'value' => 0],
        ];
    }

    protected function query() {
        $query = Article::find();

        $query->limit($this->limit);
        $query->offset($this->offset);
        $query->orderBy(['article.created_at' => SORT_DESC]);

        $userId = \Yii::$app->user->id ?? null;

        if (!is_null($userId)) {
            $query->innerJoin('user', 'article.user_id = user.id')
                ->innerJoin('follow', 'user.id = follow.followed_id')
                ->andWhere(['follow.follower_id' => $userId]);
        }

        return $query;
    }

    public function search($params) {
        $this->load($params, '');
        $models = [];
        if ($this->validate()) {
            $models = $this->query()->all();
        }

        return new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => false,
            'sort' => false,
        ]);
    }
}
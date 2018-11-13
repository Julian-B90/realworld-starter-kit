<?php


namespace app\modules\api\models;


use app\models\Favorite;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class ArticleSearch extends Article
{
    public $tag;
    public $author;
    public $favourited;
    public $limit;
    public $offset;

    public function rules()
    {
        return [
            [['tag', 'author', 'favourited'], 'string'],
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

        if (!is_null($this->author)) {
            $query->innerJoin([
                'authors' => User::find()
                    ->select(['author_id' => 'id'])
                    ->andWhere(['user.username' => $this->author])
            ], 'authors.author_id = article.user_id');
        }

        if (!is_null($this->tag)) {
            $query->innerJoin('article_tag', 'article_tag.article_id = article.id')
                ->innerJoin('tag', 'article_tag.tag_id = tag.id')
                ->andWhere(['tag.name' => $this->tag]);
        }

        if (!is_null($this->favourited)) {
            $query->innerJoin([
                'favourited' => Favorite::find()
                    ->select(['article_id'])
                    ->innerJoin('user', 'favourite.user_id = user.id')
                    ->andWhere(['user.username' => $this->favourited])
            ], 'favourited.article_id = article.id');
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
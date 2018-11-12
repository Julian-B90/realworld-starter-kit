<?php

namespace app\models\query;

use app\models\Article;
use yii\db\Query;

/**
 * Class ArticleQuery
 *
 * @package app\models\query
 * @see Article
 */
class ArticleQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        parent::init();
        $this->select('article.*');
    }

    /**
     * Add favouritesCount value to find query result
     * @return ArticleQuery
     */
    public function withFavouritesCount() {
        return $this
            ->addSelect(['following' => 'ifnull(favourites.favouritesCount, 0)'])
            ->leftJoin([
                'favourites' => (new Query())
                    ->select(['favourite.article_id', 'favouritesCount' => 'count(*)'])
                    ->from('favourite')
                    ->groupBy('favourite.article_id')

            ],'article.id = favourites.article_id');
    }
}
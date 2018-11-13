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
     * Add favoritesCount value to find query result
     * @return ArticleQuery
     */
    public function withFavoritesCount() {
        return $this
            ->addSelect(['favoritesCount' => 'ifnull(favorites.favoritesCount, 0)'])
            ->leftJoin([
                'favorites' => (new Query())
                    ->select(['favorite.article_id', 'favoritesCount' => 'count(*)'])
                    ->from('favorite')
                    ->groupBy('favorite.article_id')

            ],'article.id = favorites.article_id');
    }
}
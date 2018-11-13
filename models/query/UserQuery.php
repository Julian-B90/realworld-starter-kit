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
class UserQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        parent::init();
        $this->select('user.*');
    }

    /**
     * select user.*, ifnull(follows.following, 0) as following from user
    left join
    (
    select follow.followed_id, count(*) as following from follow
    where follow.follower_id = 1
    group by follow.followed_id
    ) as follows on user.id = follows.followed_id
     */

    /**
     * Add favoritesCount value to find query result
     * @param int $userId
     * @return UserQuery
     */
    public function withFollowing($userId) {
        return $this
            ->addSelect(['following' => 'ifnull(follows.following, 0)'])
            ->leftJoin([
                'follows' => (new Query())
                    ->select(['follow.followed_id', 'following' => 'count(*)'])
                    ->from('follow')
                    ->where(['follow.follower_id' => $userId])
                    ->groupBy('follow.followed_id')

            ],'user.id = follows.followed_id');
    }
}
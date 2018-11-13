<?php

use yii\db\Migration;

/**
 * Handles the creation of table `favourite`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `article`
 */
class m180927_200050_create_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('favorite', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'article_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-favorite-user_id',
            'favorite',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-favorite-user_id',
            'favorite',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `article_id`
        $this->createIndex(
            'idx-favorite-article_id',
            'favorite',
            'article_id'
        );

        // add foreign key for table `article`
        $this->addForeignKey(
            'fk-favorite-article_id',
            'favorite',
            'article_id',
            'article',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-favorite-user_id',
            'favorite'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-favorite-user_id',
            'favorite'
        );

        // drops foreign key for table `article`
        $this->dropForeignKey(
            'fk-favorite-article_id',
            'favorite'
        );

        // drops index for column `article_id`
        $this->dropIndex(
            'idx-favorite-article_id',
            'favorite'
        );

        $this->dropTable('favorite');
    }
}

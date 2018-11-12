<?php

use yii\db\Migration;

/**
 * Handles the creation of table `favourite`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `article`
 */
class m180927_200050_create_favourite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('favourite', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'article_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-favourite-user_id',
            'favourite',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-favourite-user_id',
            'favourite',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `article_id`
        $this->createIndex(
            'idx-favourite-article_id',
            'favourite',
            'article_id'
        );

        // add foreign key for table `article`
        $this->addForeignKey(
            'fk-favourite-article_id',
            'favourite',
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
            'fk-favourite-user_id',
            'favourite'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-favourite-user_id',
            'favourite'
        );

        // drops foreign key for table `article`
        $this->dropForeignKey(
            'fk-favourite-article_id',
            'favourite'
        );

        // drops index for column `article_id`
        $this->dropIndex(
            'idx-favourite-article_id',
            'favourite'
        );

        $this->dropTable('favourite');
    }
}

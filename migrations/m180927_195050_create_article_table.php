<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m180927_195050_create_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'slug' => $this->string()->unique(),
            'title' => $this->string(),
            'description' => $this->string(),
            'body' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-article-user_id',
            'article',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-article-user_id',
            'article',
            'user_id',
            'user',
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
            'fk-article-user_id',
            'article'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-article-user_id',
            'article'
        );

        $this->dropTable('article');
    }
}

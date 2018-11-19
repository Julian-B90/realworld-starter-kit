<?php

use yii\db\Migration;

/**
 * Handles the creation of table `follow`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `user`
 */
class m180927_200223_create_follow_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('follow', [
            'id' => $this->primaryKey(),
            'follower_id' => $this->integer()->notNull(),
            'followed_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // creates index for column `follower_id`
        $this->createIndex(
            'idx-follow-follower_id',
            'follow',
            'follower_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-follow-follower_id',
            'follow',
            'follower_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `followed_id`
        $this->createIndex(
            'idx-follow-followed_id',
            'follow',
            'followed_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-follow-followed_id',
            'follow',
            'followed_id',
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
            'fk-follow-follower_id',
            'follow'
        );

        // drops index for column `follower_id`
        $this->dropIndex(
            'idx-follow-follower_id',
            'follow'
        );

        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-follow-followed_id',
            'follow'
        );

        // drops index for column `followed_id`
        $this->dropIndex(
            'idx-follow-followed_id',
            'follow'
        );

        $this->dropTable('follow');
    }
}

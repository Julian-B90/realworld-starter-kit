<?php

use yii\db\Migration;

/**
 * Class m180930_205413_rename_password_to_password_hash_column_on_user_table
 */
class m180930_205413_rename_password_to_password_hash_column_on_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('user', 'password', 'password_hash');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('user', 'password_hash', 'password');
    }

}

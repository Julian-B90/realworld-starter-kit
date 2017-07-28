<?php

use yii\db\Migration;

class m170728_083145_add_first_user_to_user_table extends Migration
{
    public function safeUp()
    {
      $this->insert('user', [
        'email' => 'jake@jake.jake',
        'password' => 'jakejake',
        'token' => 'jwt.token.here',
        'username' => 'jake',
        'bio' => 'I work at statefarm',
        'image' => null
      ]);
    }

    public function safeDown()
    {
      $this->delete('user', [
        'email' => 'jake@jake.jake',
      ]);
    }
}

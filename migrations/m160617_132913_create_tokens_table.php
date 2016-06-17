<?php

use yii\db\Migration;

/**
 * Handles the creation for table `tokens_table`.
 */
class m160617_132913_create_tokens_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%lnch_user_tokens}}', [
            'user_id'       => $this->integer(11)->notNull(),
            'code'          => $this->string(32)->notNull(),
            'created_at'    => $this->integer(26),
            'type'          => $this->smallInteger()->notNull(),
        ]);

        $this->createIndex('token_unique', '{{%lnch_user_tokens}}', ['user_id', 'code', 'type'], true);

        $this->addForeignKey('fk_user_token', '{{%lnch_user_tokens}}', 'user_id', '{{%lnch_users}}', 'id', 'CASCADE', 'RESTRICT');
    }
    public function down()
    {
        $this->dropTable('{{%lnch_user_tokens}}');
    }
}

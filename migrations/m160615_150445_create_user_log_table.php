<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_log`.
 */
class m160615_150445_create_user_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%lnch_user_log}}', [
            'id'                => $this->primaryKey(),
            'user_id'           => $this->integer(11)->notNull(),
            'session_id'        => $this->string(256),
            'log_date'          => $this->dateTime(),
            'action'            => $this->string(56),
            'message'           => $this->text(),
            'user_ip'           => $this->string(128),
            'http_user_agent'   => $this->text()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%lnch_user_log}}');
    }
}

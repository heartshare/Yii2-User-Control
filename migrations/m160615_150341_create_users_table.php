<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Handles the creation for table `users`.
 */
class m160615_150341_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%lnch_users}}', [
            'id'                    => $this->primaryKey(),
            'username'              => $this->string(256)->notNull(),
            'password_hash'         => $this->string(256)->notNull(),
            'email'                 => $this->string(256)->notNull(),
            'auth_key'              => $this->string(256)->notNull(),
            'user_type'             => $this->integer(5)->defaultValue(10),
            'creation_date'         => $this->dateTime(),
            'confirmation_date'     => $this->dateTime(),
            'last_updated'          => $this->dateTime(),
            'last_login'            => $this->dateTime(),
            'status'                => $this->string(5)->defaultValue('P'),
            'signup_ip'             => $this->string(64),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%lnch_users}}');
    }
}

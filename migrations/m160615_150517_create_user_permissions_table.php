<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_permissions`.
 */
class m160615_150517_create_user_permissions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%lnch_user_permissions}}', [
            'group'         => $this->string(256)->notNull(),
            'permission'    => $this->string(256)->notNull(),
            'user_id'       => $this->integer(11)
        ]);

        $this->addPrimaryKey('pk-user_permissions', '{{%lnch_user_permissions}}', ['group', 'permission', 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%lnch_user_permissions}}');
    }
}

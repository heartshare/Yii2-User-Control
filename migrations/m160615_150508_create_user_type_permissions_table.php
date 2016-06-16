<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_type_permissions`.
 */
class m160615_150508_create_user_type_permissions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('lnch_user_type_permissions', [
            'group'         => $this->string(256)->notNull(),
            'permission'    => $this->string(256)->notNull(),
            'user_types'    => $this->string(256)
        ]);

        $this->addPrimaryKey('pk-user_type_permissions', 'lnch_user_type_permissions', ['group', 'permission']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('lnch_user_type_permissions');
    }
}

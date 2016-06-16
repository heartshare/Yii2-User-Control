<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_types`.
 */
class m160615_150459_create_user_types_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('lnch_user_types', [
            'type_id'       => $this->primaryKey(),
            'name'          => $this->string(256)->notNull(),
            'alias'         => $this->string(256),
            'description'   => $this->text()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('lnch_user_types');
    }
}

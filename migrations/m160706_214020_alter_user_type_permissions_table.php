<?php

use yii\db\Migration;

class m160706_214020_alter_user_type_permissions_table extends Migration
{
    public function up()
    {
        $this->renameColumn('{{%lnch_user_type_permissions}}', 'user_types', 'min_user_type');

        $this->alterColumn('{{%lnch_user_type_permissions}}', 'min_user_type', $this->integer(11));
    }

    public function down()
    {
        $this->alterColumn('{{%lnch_user_type_permissions}}', 'min_user_type', $this->string(256));

        $this->renameColumn('{{%lnch_user_type_permissions}}', 'min_user_type', 'user_types');
    }
}

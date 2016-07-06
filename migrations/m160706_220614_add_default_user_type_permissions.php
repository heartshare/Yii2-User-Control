<?php

use yii\db\Migration;

class m160706_220614_add_default_user_type_permissions extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%lnch_user_type_permissions}}', ['group', 'permission', 'min_user_type'], [
            ['users', 'view-profiles', 10],

            ['users', 'admin-access', 20],
            ['users', 'edit-user', 20],
            ['users', 'confirm-user', 20],
            ['users', 'ban-user', 20],
            ['users', 'edit-user-profiles', 20],
            ['users', 'view-user-log', 20],
            
            ['users', 'change-user-type', 30],
            ['users', 'delete-user', 30],
            ['users', 'export-user-log', 30],
            ['users', 'create-user', 30],
            ['users', 'view-user-types', 20],
            ['users', 'edit-user-type-alias', 30],
            ['users', 'assign-user-type-permission', 30],
            ['users', 'assign-user-permission', 30],
            
            ['users', 'create-permission', 40],        
        ]);
    }

    public function down()
    {
        
    }
}

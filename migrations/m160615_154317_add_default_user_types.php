<?php

use yii\db\Migration;

class m160615_154317_add_default_user_types extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%lnch_user_types}}', ['type_id', 'name', 'description'], [
            [10, 'User',            'Basic user, has read only access to limited areas of the site.'],
            [20, 'Moderator',       'Moderators can read and edit certain areas of the site. They cannot edit sensitive information.'],
            [30, 'Administrator',   'Admins are able to read, edit and delete data. They have access to the majority of the site.'],
            [40, 'Founder',         'Founders have unlimited access to the site with no restrictions. Be wary of who you give Founder access to'],
        ]);
    }

    public function down()
    {
        echo "m160615_154317_add_default_user_types cannot be reverted.\n";
    }
}

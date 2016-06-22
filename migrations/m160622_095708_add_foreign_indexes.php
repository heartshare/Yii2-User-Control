<?php

use yii\db\Migration;

class m160622_095708_add_foreign_indexes extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE {{%lnch_users}} ENGINE = InnoDB;");
        $this->execute("ALTER TABLE {{%lnch_user_profiles}} ENGINE = InnoDB;");
        $this->execute("ALTER TABLE {{%lnch_user_tokens}} ENGINE = InnoDB;");
        $this->execute("ALTER TABLE {{%lnch_user_permissions}} ENGINE = InnoDB;");

        $this->createIndex('user_unique_username', '{{%lnch_users}}', 'username', true);
        $this->createIndex('user_unique_email', '{{%lnch_users}}', 'email', true);

        $this->addForeignKey('fk-lnch_user_profiles-lnch_users', 
            '{{%lnch_user_profiles}}', 'user_id',
            '{{%lnch_users}}', 'id', 
            'CASCADE', 'RESTRICT');

        $this->addForeignKey('fk-lnch_user_tokens-lnch_users', 
            '{{%lnch_user_tokens}}', 'user_id',
            '{{%lnch_users}}', 'id', 
            'CASCADE', 'RESTRICT');

        $this->addForeignKey('fk-lnch_user_permissions-lnch_users', 
            '{{%lnch_user_permissions}}', 'user_id',
            '{{%lnch_users}}', 'id', 
            'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('fk-lnch_user_profiles-lnch_users', '{{%lnch_user_profiles}}');
        $this->dropForeignKey('fk-lnch_user_tokens-lnch_users', '{{%lnch_user_tokens}}');
        $this->dropForeignKey('fk-lnch_user_permissions-lnch_users', '{{%lnch_user_permissions}}');

        $this->dropIndex('user_unique_username', '{{%lnch_users}}');
        $this->dropIndex('user_unique_email', '{{%lnch_users}}');
    }
}

<?php

use yii\db\Migration;

class m160622_115111_add_unconfirmed_email_field extends Migration
{
    public function up()
    {
        $this->addColumn('{{%lnch_users}}', 'unconfirmed_email', $this->string(256));

        $this->execute("ALTER TABLE {{%lnch_users}} CHANGE COLUMN unconfirmed_email unconfirmed_email VARCHAR(256) AFTER email;");
    }

    public function down()
    {
        $this->dropColumn('{{%lnch_users}}', 'unconfirmed_email');
    }
}

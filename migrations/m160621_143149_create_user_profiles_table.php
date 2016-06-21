<?php

use yii\db\Migration;

/**
 * Handles the creation for table `user_profiles_table`.
 */
class m160621_143149_create_user_profiles_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%lnch_user_profiles}}', [
            'user_id'           => $this->primaryKey(),
            'title'             => $this->string(56),
            'first_name'        => $this->string(256),
            'middle_names'      => $this->string(256),
            'surname'           => $this->string(256),
            'date_of_birth'     => $this->date(),
            'location'          => $this->string(256),
            'website'           => $this->string(256),
            'bio'               => $this->text(),
            'timezone'          => $this->string(56),
            'job_title'         => $this->string(256),
            'contact_number'    => $this->string(256),
            'language'          => $this->string(56)       
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%lnch_user_profiles}}');
    }
}

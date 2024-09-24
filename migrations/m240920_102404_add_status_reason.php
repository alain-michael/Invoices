<?php

use yii\db\Migration;

/**
 * Class m240920_102404_add_status_reason
 */
class m240920_102404_add_status_reason extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transactions}}', 'reason', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transactions}}', 'reason');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240920_102404_add_status_reason cannot be reverted.\n";

        return false;
    }
    */
}

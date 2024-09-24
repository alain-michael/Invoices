<?php

use yii\db\Migration;

/**
 * Class m240918_085701_add_transaction_date
 */
class m240918_085701_add_transaction_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transactions}}', 'transaction_date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transactions}}', 'transaction_date');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240918_085701_add_transaction_date cannot be reverted.\n";

        return false;
    }
    */
}

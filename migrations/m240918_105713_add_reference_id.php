<?php

use yii\db\Migration;

/**
 * Class m240918_105713_add_reference_id
 */
class m240918_105713_add_reference_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%transactions}}', 'reference_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%transactions}}', 'reference_id');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240918_105713_add_reference_id cannot be reverted.\n";

        return false;
    }
    */
}

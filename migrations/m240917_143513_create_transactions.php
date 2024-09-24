<?php

use yii\db\Migration;

/**
 * Class m240917_143513_create_transactions
 */
class m240917_143513_create_transactions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'amount' => $this->integer()->notNull(),
            'number' => $this->integer()->notNull(),
            'status' => $this->string()->defaultValue('Pending'),
        ]
        );

        $this->createIndex('{{%idx_transactions_number}}', '{{%transactions}}', 'number');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx_transactions_number}}',
            '{{%donations}}'
        );

        $this->dropTable('{{%transactions}}');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240917_143513_create_transactions cannot be reverted.\n";

        return false;
    }
    */
}

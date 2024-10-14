<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%items}}`.
 */
class m240927_101509_create_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%invoice_items}}', [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'quantity' => $this->integer()->notNull()->defaultValue(1),
            'price' => $this->decimal(10,2)->notNull(),
            'item_amount' => $this->decimal(10,2)->notNull(),
            'description' => $this->string(),
            'image' => $this->string(),
            'FOREIGN KEY (invoice_id) REFERENCES invoice(id) ON DELETE CASCADE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%items}}');
    }
}

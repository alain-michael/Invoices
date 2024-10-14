<?php

use yii\db\Migration;

class m230502_000001_create_invoice_charge_table extends Migration
{
    public function up()
    {
        $this->createTable('invoice_charge', [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'value' => $this->decimal(10, 2)->notNull(),
            'FOREIGN KEY (invoice_id) REFERENCES invoice(id) ON DELETE CASCADE',
        ]);

    }

    public function down()
    {
        $this->dropTable('invoice_charge');
    }
}
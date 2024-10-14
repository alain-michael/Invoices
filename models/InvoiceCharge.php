<?php

namespace app\models;

use Yii;

class InvoiceCharge extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'invoice_charge';
    }

    public function rules()
    {
        return [
            [['invoice_id', 'name', 'type', 'value'], 'required'],
            [['invoice_id'], 'integer'],
            [['value'], 'number'],
            [['name', 'type'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'name' => 'Charge Name',
            'type' => 'Charge Type',
            'value' => 'Charge Value',
        ];
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::class, ['id' => 'invoice_id']);
    }
}
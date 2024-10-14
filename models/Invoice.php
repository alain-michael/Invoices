<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class Invoice extends \yii\db\ActiveRecord
{
    public $additionalCharges = [];

    public static function tableName()
    {
        return 'invoice';
    }

    public function rules()
    {
        return [
            [['customer_name'], 'required'],
            [['total_amount', 'subtotal'], 'number'],
            [['created_at'], 'safe'],
            [['customer_name'], 'string', 'max' => 255],
            [['additionalCharges'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subtotal' => 'Subtotal',
            'total_amount' => 'Total Amount',
            'customer_name' => 'Customer Name',
            'created_at' => "Invoice Date (Leave Blank for today's date)",
        ];
    }

    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function () {
                    return !empty($this->created_at) ? $this->created_at : date('Y-m-d');
                },
            ]
        ];
    }

    public function getInvoiceItems()
    {
        return $this->hasMany(InvoiceItems::class, ['invoice_id' => 'id']);
    }

    public function getInvoiceCharges()
    {
        return $this->hasMany(InvoiceCharge::class, ['invoice_id' => 'id']);
    }

    public function getAdditionalChargesTotal()
    {
        $total = 0;
        foreach ($this->additionalCharges as $charge) {
            if ($charge['type'] === 'percentage') {
                $total += $this->subtotal * ($charge['value'] / 100);
            } else {
                $total += $charge['value'];
            }
        }
        return $total;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->total_amount = $this->subtotal + $this->getAdditionalChargesTotal();
            return true;
        }
        return false;
    }
}
<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice_items".
 *
 * @property int $id
 * @property int $invoice_id
 * @property string $name
 * @property int $quantity
 * @property float $price
 * @property float $item_amount
 * @property string|null $description
 * @property string|null $image
 *
 * @property Invoice $invoice
 */
class InvoiceItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoice_id', 'name', 'price', 'item_amount', 'quantity'], 'required'],
            [['invoice_id', 'quantity'], 'integer'],
            [['price', 'item_amount'], 'number'],
            [['name', 'description', 'image'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'name' => 'Name',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'item_amount' => 'Item Amount',
            'description' => 'Description',
            'image' => 'Image',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->item_amount = $this->price * $this->quantity;
            return true;
        }
        return false;
    }


    /**
     * Gets query for [[Invoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::class, ['id' => 'invoice_id']);
    }
}

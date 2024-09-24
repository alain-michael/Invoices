<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "Transactions".
 *
 * @property int $id
 * @property string|null $title
 * @property int $amount
 * @property int $number
 * @property string|null $status
 */
class Transactions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Transactions';
    }

    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'transaction_date',
                'updatedAtAttribute' => false,
                'value' => date('Y-m-d H:i:s'), // Or 'value' => time() if you want to store as UNIX timestamp
            ]
            ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'number'], 'required'],
            [['amount', 'number'], 'integer'],
            [['title', 'status'], 'string', 'max' => 255],
            [['number'], 'match', 'pattern' => '/^07[89]\d{7}$/', 'message' => 'Invalid MTN number.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'amount' => 'Amount',
            'number' => 'Number',
            'status' => 'Status',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TransactionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionsQuery(get_called_class());
    }
}

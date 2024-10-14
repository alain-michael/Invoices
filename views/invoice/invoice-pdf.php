<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $model->id ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .invoice-header {
            border-bottom: 2px solid #007bff;
            margin-top: -40px;
            padding-bottom: 10px;
        }
        h1 {
            color: #007bff;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
        }
        .additional-charges {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>Invoice #<?= $model->id ?></h1>
    </div>
    
    <div class="invoice-details">
        <div>
            <p><strong>Customer:</strong> <?= $model->customer_name ?></p>
            <p><strong>Date:</strong> <?= Yii::$app->formatter->asDate($model->created_at, 'php:F j, Y') ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item->name ?></td>
                <td><?= $item->quantity ?></td>
                <td><?= Yii::$app->formatter->asCurrency($item->price) ?></td>
                <td><?= Yii::$app->formatter->asCurrency($item->item_amount) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        <p>Subtotal: <?= Yii::$app->formatter->asCurrency($model->subtotal) ?></p>
    </div>

    <?php if (!empty($charges)): ?>
    <div class="additional-charges">
        <h3>Additional Charges</h3>
        <table>
            <thead>
                <tr>
                    <th>Charge Name</th>
                    <th>Value</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($charges as $charge): ?>
                <tr>
                    <td><?= $charge->name ?></td>
                    <td>
                        <?= $charge->type === 'percentage' 
                            ? $charge->value . '%' 
                            : Yii::$app->formatter->asCurrency($charge->value) 
                        ?>
                    </td>
                    <td>
                        <?= Yii::$app->formatter->asCurrency(
                            $charge->type === 'percentage' 
                                ? $model->subtotal * ($charge->value / 100) 
                                : $charge->value
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="total">
        <p>Total: <?= Yii::$app->formatter->asCurrency($model->total_amount) ?></p>
    </div>
</body>
</html>
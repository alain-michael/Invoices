<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $model->id ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        p {
            font-size: 1.1em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tfoot td {
            font-weight: bold;
        }
        .total {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>Invoice #<?= $model->id ?></h1>
<p>Customer: <strong><?= $model->customer_name ?></strong></p>
<p>Date: <strong><?= date('F j, Y', strtotime($model->created_at)) ?></strong></p>

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
            <td><?= htmlspecialchars($item->name) ?></td>
            <td><?= htmlspecialchars($item->quantity) ?></td>
            <td><?= htmlspecialchars(number_format($item->price, 2)) ?></td>
            <td><?= htmlspecialchars(number_format($item->item_amount, 2)) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="total">Total:</td>
            <td><?= htmlspecialchars(number_format($model->total_amount, 2)) ?></td>
        </tr>
    </tfoot>
</table>

</body>
</html>

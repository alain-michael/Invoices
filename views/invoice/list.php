<?php
use yii\helpers\Html;

$this->title = 'Invoices List';
?>

<div class="invoice-index">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <?= Html::a('<i class="bi bi-plus-lg"></i> Add New Invoice', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="card-body">
            <input type="text" id="searchInput" placeholder="Search invoices..." class="form-control mb-3" />

            <table class="table table-hover" id="invoicesTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Customer Name</th>
                        <th>Invoice Date</th>
                        <th class="text-end">Total Amount</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= Html::encode($invoice->id) ?></td>
                            <td>
                                <?= Html::a(Html::encode($invoice->customer_name), ['view-invoice', 'id' => $invoice->id], [
                                    'class' => 'text-decoration-none'
                                ]) ?>
                            </td>
                            <td><?= Yii::$app->formatter->asDate($invoice->created_at, 'php:M d, Y') ?></td>
                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($invoice->total_amount) ?></td>
                            <td>
                                <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $invoice->id], [
                                    'class' => 'btn btn-outline-primary btn-sm',
                                    'title' => 'View',
                                ]) ?>
                                <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $invoice->id], [
                                    'class' => 'btn btn-outline-info btn-sm mx-1',
                                    'title' => 'Update',
                                ]) ?>
                                <?= Html::a('<i class="bi bi-trash"></i>', ['delete-invoice', 'id' => $invoice->id], [
                                    'class' => 'btn btn-outline-danger btn-sm',
                                    'title' => 'Delete',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this invoice?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- No results message -->
            <div id="noResultsMessage" class="text-center text-muted mt-3" style="display: none;">
                No invoices found.
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#invoicesTable tbody tr');
    let hasResults = false;

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let i = 0; i < cells.length - 1; i++) {
            if (cells[i].textContent.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }

        row.style.display = found ? '' : 'none';
        if (found) hasResults = true;
    });

    document.getElementById('noResultsMessage').style.display = hasResults ? 'none' : 'block';
});
</script>

<style>
th, td {
    text-align: center !important;
}
</style>

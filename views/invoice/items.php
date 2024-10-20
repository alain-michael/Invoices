<?php
use yii\helpers\Html;

$this->title = 'Items';
?>

<div class="items-index">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <?= Html::a('<i class="bi bi-plus-lg"></i> Add New Item', ['add-item'], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="card-body">
            <input type="text" id="searchInput" placeholder="Search items..." class="form-control mb-3" />

            <table class="table table-hover" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th class="text-end">Price</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider->models as $model): ?>
                        <tr>
                            <td><?= Html::encode($model->id) ?></td>
                            <td>
                                <?= Html::a(Html::encode($model->name), ['view-item', 'id' => $model->id], [
                                    'class' => 'text-decoration-none'
                                ]) ?>
                            </td>
                            <td><?= Html::encode($model->description) ?></td>
                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->price) ?></td>
                            <td>
                                <?= Html::a('<i class="bi bi-pencil"></i>', ['update-item', 'id' => $model->id], [
                                    'class' => 'btn btn-outline-info btn-sm',
                                    'title' => 'Update',
                                ]) ?>
                                <?= Html::a('<i class="bi bi-trash"></i>', ['delete-item', 'id' => $model->id], [
                                    'class' => 'btn btn-outline-danger btn-sm ms-1',
                                    'title' => 'Delete',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this item?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="noResultsMessage" class="text-center text-muted mt-3" style="display: none;">
                No invoices found.
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#itemsTable tbody tr');
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

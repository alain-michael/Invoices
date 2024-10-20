<?php

use yii\helpers\Html;

$this->title = 'Dashboard';
?>

<div class="dashboard">
    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Invoices</h6>
                            <h2 class="card-title mb-0"><?= $totalInvoices ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Revenue</h6>
                            <h2 class="card-title mb-0">$<?= number_format($totalRevenue, 2) ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">Total Items</h6>
                            <h2 class="card-title mb-0"><?= $totalItems ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2">This Month</h6>
                            <h2 class="card-title mb-0">$<?= number_format($monthlyRevenue, 2) ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices and Items -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Invoices</h5>
                    <?= Html::a('View All', ['invoice/list'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentInvoices as $invoice): ?>
                                    <tr>
                                        <td><?= $invoice->id ?></td>
                                        <td><?= Html::encode($invoice->customer_name) ?></td>
                                        <td><?= Yii::$app->formatter->asDate($invoice->created_at) ?></td>
                                        <td class="text-end">$<?= number_format($invoice->total_amount, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Popular Items</h5>
                    <?= Html::a('View All', ['invoice/items'], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($popularItems as $item): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?= Html::encode($item['name']) ?></h6>
                                    <small class="text-muted">$<?= number_format($item['price'], 2) ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?= $item['sales_count'] ?> <?=$item['sales_count'] > 1 ? "Sales": "Sale"?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
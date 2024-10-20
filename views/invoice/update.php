<?php

use app\models\InvoiceItems;
use app\models\Items;
use yii\bootstrap5\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JqueryAsset;

$this->title = 'Update Invoice: ' . $model->id;
?>

<div class="invoice-update">
    <div class="invoice-form mx-4">
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true, 'class' => 'form-control'])->label('Customer Name') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'created_at')->input('date', ['class' => 'form-control'])->label('Invoice Date') ?>
            </div>
        </div>

        <h4 class="mb-3">Invoice Items</h4>
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'limit' => 10,
            'min' => 1,
            'insertButton' => '.add-item',
            'deleteButton' => '.remove-item',
            'model' => $items[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'item_id',
                'quantity',
                'price',
                'item_amount',
            ],
        ]); ?>

        <div class="container-items">
            <?php foreach ($items as $i => $item): ?>
                <div class="item card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Item <?= $i + 1 ?></h5>
                        <div>
                            <button type="button" class="add-item btn btn-success btn-sm"><i class="bi bi-plus"></i> Add</button>
                            <button type="button" class="remove-item btn btn-danger btn-sm"><i class="bi bi-dash"></i> Remove</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($item, "[{$i}]id")->dropDownList(
                                    ArrayHelper::map(Items::find()->all(), 'id', function ($model) {
                                        return $model->name . ' (Price: $' . $model->price . ')';
                                    }),
                                    ['prompt' => 'Select Item', 'class' => 'form-select item-select select2']
                                ) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($item, "[{$i}]price")->textInput(['disabled' => true, 'readonly' => true, 'class' => 'form-control price-input'])->label('Price') ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($item, "[{$i}]quantity")->textInput(['type' => 'number', 'min' => '1', 'class' => 'form-control quantity-input'])->label('Quantity') ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($item, "[{$i}]item_amount")->textInput(['readonly' => true, 'class' => 'form-control amount-input'])->label('Amount') ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php DynamicFormWidget::end(); ?>

        <div class="total-amount mt-3">
            <h5>Total: <span id="invoice-total">$<?= number_format($model->total_amount, 2) ?></span></h5>
        </div>

        <div class="form-group mt-4">
            <?= Html::submitButton('Update Invoice', ['class' => 'btn btn-primary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs("
    $('.dynamicform_wrapper').on('change', '.item-select', function() {
        var row = $(this).closest('.item');
        var priceInput = row.find('.price-input');

        var selectedOption = $(this).find('option:selected');
        var priceText = selectedOption.text();
        var price = priceText.match(/\\$(\\d+(\\.\\d{1,2})?)/);
        
        if (price) {
            priceInput.val(price[0].replace('$', ''));
            updateAmount(row);
        }
    });

    $('.dynamicform_wrapper').on('input', '.quantity-input', function() {
        updateAmount($(this).closest('.item'));
    });

    function updateAmount(row) {
        var price = parseFloat(row.find('.price-input').val()) || 0;
        var quantity = parseInt(row.find('.quantity-input').val()) || 0;
        var amount = price * quantity;
        row.find('.amount-input').val(amount.toFixed(2));
        updateTotal();
    }

    function updateTotal() {
        var total = 0;
        $('.amount-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#invoice-total').text('$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
    }

    $('.dynamicform_wrapper').on('afterInsert', function(e, item) {
        // Update item number
        var itemCount = $('.container-items .item').length;
        $(item).find('.card-title').text('Item ' + itemCount);
        $(item).find('input').val('');
        $(item).find('select').val('').trigger('change'); // Reset select2
    });

    $('.dynamicform_wrapper').on('afterDelete', function(e) {
        updateTotal();
        // Update item numbers after deletion
        $('.container-items .item').each(function(index) {
            $(this).find('.card-title').text('Item ' + (index + 1));
        });
    });
");
?>
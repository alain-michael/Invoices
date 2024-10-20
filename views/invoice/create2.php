<?php

use app\models\InvoiceItems;
use app\models\Items;
use yii\bootstrap5\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JqueryAsset;

JqueryAsset::register($this);


$this->title = 'Create Invoice';
?>
<style>
    .total-amount {
        text-align: right;
        width: 100%;
        display: block;
    }
</style>
<div class="invoice-form mt-4 mx-4">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="row">
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

    <div class="additional-charges">
        <h4>Additional Charges</h4>
        <div id="charges-container">
            <!-- Charges will be added here dynamically -->
        </div>
        <button type="button" id="add-charge" class="btn btn-success btn-sm mt-2">
            <i class="bi bi-plus"></i> Add Charge
        </button>
    </div>

    <div class="total-amount mt-3">
        <h6>Subtotal: <span id="invoice-subtotal">$0.00</span><br></h6>
        <h6>Additional Charges: <span id="additional-charges-total">$0.00</span><br></h6>
        <div class="total-amount">
            <h5>Total: <span id="invoice-total">$0.00</span></h5>
        </div>
    </div>



    <div class="form-group mt-4">
        <?= Html::submitButton('Create Invoice', ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    function addCharge() {
        const chargeContainer = document.getElementById('charges-container');
        const chargeIndex = chargeContainer.children.length || 0;
        console.log(chargeIndex);
        const chargeHtml = `
            <div class="charge-item">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="charges[${chargeIndex}][name]" class="form-control" placeholder="Charge Name">
                    </div>
                    <div class="col-md-3">
                        <select name="charges[${chargeIndex}][type]" class="form-control charge-type">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="charges[${chargeIndex}][value]" class="form-control charge-value" step="0.01" min="0">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-charge">Remove</button>
                    </div>
                </div>
            </div>
        `;
        chargeContainer.insertAdjacentHTML('beforeend', chargeHtml);
    }

    // Add charge button event listener
    document.getElementById('add-charge').addEventListener('click', addCharge);

    // Remove charge button event listener
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-charge')) {
            e.target.closest('.charge-item').remove();
            updateTotals();
        }
    });

    // Function to calculate additional charges
    function calculateAdditionalCharges(subtotal) {
        let totalCharges = 0;
        document.querySelectorAll('.charge-item').forEach(function(item) {
            const type = item.querySelector('.charge-type').value;
            const value = parseFloat(item.querySelector('.charge-value').value) || 0;
            if (type === 'percentage') {
                totalCharges += subtotal * (value / 100);
            } else {
                totalCharges += value;
            }
        });
        return totalCharges;
    }

    // Function to update all totals
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('input[name*="item_amount"]').forEach(function(input) {
            subtotal += parseFloat(input.value) || 0;
        });
        const additionalCharges = calculateAdditionalCharges(subtotal);
        const total = subtotal + additionalCharges;

        document.getElementById('invoice-subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('additional-charges-total').textContent = '$' + additionalCharges.toFixed(2);
        document.getElementById('invoice-total').textContent = '$' + total.toFixed(2);
    }

    // Update totals when charges change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('charge-value') || e.target.classList.contains('charge-type')) {
            updateTotals();
        }
    });

    // Modify existing updateInvoiceTotal function to call updateTotals
    function updateInvoiceTotal() {
        updateTotals();
    }


    // Update total when item amounts change
    document.addEventListener("input", function(event) {
        if (event.target.name.includes('item_amount') || event.target.name.includes('price') || event.target.name.includes('quantity')) {
            setTimeout(updateInvoiceTotal, 0);
        }
    });

    // Update total after adding or removing items
    $(".dynamicform_wrapper").on("afterInsert afterDelete", function(e, item) {
        updateInvoiceTotal();
    });


    // Initial call to set the total on page load
    updateInvoiceTotal();
</script>

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
        $('#invoice-total').text('$' + total.toFixed(2));
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

    // Initialize Select2 for existing and future selects
    $('.select2').select2({
        placeholder: 'Select an item',
        allowClear: true
    });
");
?>
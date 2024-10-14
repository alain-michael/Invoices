<?php

use yii\bootstrap5\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use \yii\web\JqueryAsset;

JqueryAsset::register($this);

$this->title = 'Create Invoice';
?>
<style>
    :root {
        --primary-color: #007bff;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --light-gray: #f8f9fa;
        --dark-gray: #343a40;
    }

    body {
        font-family: 'Roboto', Arial, sans-serif;
        background-color: var(--light-gray);
        color: var(--dark-gray);
    }

    .invoice-form {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 30px;
    }

    .invoice-form h2 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 10px;
        margin-bottom: 30px;
    }

    .invoice-form h4 {
        color: var(--secondary-color);
        margin-top: 30px;
        margin-bottom: 20px;
    }

    .item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .item:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .item .card-header {
        background-color: var(--light-gray);
        border-bottom: none;
    }

    .item .card-body {
        padding: 20px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .btn-success {
        background-color: var(--success-color);
        border-color: var(--success-color);
    }

    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }

    input[readonly] {
        background-color: var(--light-gray) !important;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    .card-header .btn {
        margin-left: 5px;
    }

    .total-amount {
        font-size: 1.2em;
        font-weight: bold;
        text-align: right;
        margin-top: 20px;
    }
    .additional-charges {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .additional-charges h4 {
        color: var(--secondary-color);
        margin-bottom: 20px;
    }

    .charge-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

</style>
</head>

<body>
    <div class="invoice-form container mt-4 w-75">
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <h2><?= Html::encode($this->title) ?></h2>

        <div class="row mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'created_at')->input('date') ?>
            </div>
        </div>

        <h4>Invoice Items</h4>
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
                'name',
                'quantity',
                'price',
                'item_amount',
            ],
        ]); ?>

        <div class="container-items mb-3">
            <?php foreach ($items as $i => $item): ?>
                <div class="item card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#6c757d44;">
                        <h5 class="card-title mb-0">Item <?= $i + 1 ?></h5>
                        <div>
                            <button type="button" class="add-item btn btn-success btn-sm"><i class="bi bi-plus"></i> Add</button>
                            <button type="button" class="remove-item btn btn-danger btn-sm"><i class="bi bi-dash"></i> Remove</button>
                        </div>
                    </div>

                    <div class="collapse show">
                        <div class="card-body">
                            <?php if (!$item->isNewRecord): ?>
                                <?= Html::activeHiddenInput($item, "[{$i}]id"); ?>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($item, "[{$i}]name")->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($item, "[{$i}]quantity")->textInput(['type' => 'number', 'min' => '1']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($item, "[{$i}]price")->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($item, "[{$i}]item_amount")->textInput(['readonly' => true]) ?>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12 text-end">
                                    <button type="button" class="confirm-item btn btn-primary" disabled>Confirm Item</button>
                                    <button type="button" class="edit-item btn btn-secondary d-none">Edit Item</button>
                                </div>
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

        <div class="total-amount">
            Subtotal: <span id="invoice-subtotal">$0.00</span><br>
            Additional Charges: <span id="additional-charges-total">$0.00</span><br>
            Total: <span id="invoice-total">$0.00</span>
        </div>

        <div class="form-group mt-4">
            <?= Html::submitButton($model->isNewRecord ? 'Create Invoice' : 'Update Invoice', ['class' => 'btn btn-primary']) ?>
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

    // Function to update the total invoice amount
    // function updateInvoiceTotal() {
    //     let total = 0;
    //     document.querySelectorAll('input[name*="item_amount"]').forEach(function(input) {
    //         total += parseFloat(input.value) || 0;
    //     });
    //     document.getElementById('invoice-total').textContent = '$' + total.toFixed(2);
    // }

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
    $js2 = <<<JS
    // Function to enable Confirm button if all required fields are filled
    function checkFields(card) {
        const inputs = card.querySelectorAll('input[name*="[name]"], input[name*="[quantity]"], input[name*="[price]"]');
        let allFilled = true;
        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                allFilled = false;
            }
        });
        return allFilled;
    }

    // Function to toggle Confirm button based on field values
    function toggleConfirmButton(card) {
        const confirmButton = card.querySelector('.confirm-item');
        if (checkFields(card)) {
            confirmButton.removeAttribute('disabled');
        } else {
            confirmButton.setAttribute('disabled', 'disabled');
    }
    }

    // Handle field input events to check if all fields are filled
    document.addEventListener("input", function(event) {
        const card = event.target.closest('.item');
        if (card) {
            toggleConfirmButton(card);
        }
    });

    function toggleCollapse() {
        $('.card-header, .card-title').on('click', function(e) {
        if ($(e.target).hasClass('add-item')) { 
            return;
        }

        const card = e.target.closest('.card');
        const cardBody = card.querySelector('.card .collapse');

        if (cardBody.classList.contains('show')) {
            $(cardBody).collapse('hide');
        } else {
            $(cardBody).collapse('show');
        }
});

    }

    toggleCollapse();

    // Handle Confirm button click
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains('confirm-item')) {
            const card = event.target.closest('.card');
            const cardBody = card.querySelector('.card .collapse');

            // Check if all fields are filled
            if (!checkFields(card)) {
                alert('Please fill in all fields before confirming.');
                return;
            }

            $('.card-title', card).text($('[name*="[name]"]', cardBody).val())

            $(cardBody).collapse('hide');

            // Disable the input fields
            card.querySelectorAll('input').forEach(function(input) {
                input.setAttribute('readonly', 'readonly');
            });

            // Change Confirm button to Edit button
            event.target.textContent = 'Confirmed';
            event.target.classList.add('btn-success');
            event.target.setAttribute('readonly', 'readonly');

            const editButton = card.querySelector('.edit-item');
            editButton.classList.remove('d-none');
        }
    });

    // Handle Edit button click to re-enable fields and expand the card
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains('edit-item')) {
            const card = event.target.closest('.card');
            const cardBody = card.querySelector('.card-body');

            $(cardBody).collapse('show');

            // Enable the input fields for editing
            card.querySelectorAll('input').forEach(function(input) {
                if(/.*item_amount/.test(input.id)){
                    return
                }
                input.removeAttribute('readonly');
            });

            // Show Confirm button again
            const confirmButton = card.querySelector('.confirm-item');
            confirmButton.textContent = 'Confirm Item';
            confirmButton.classList.remove('btn-success');
            confirmButton.removeAttribute('readonly');

            // Hide Edit button
            event.target.classList.add('d-none');
        }
    });

    // Ensure all item labels are correctly numbered
    function updateItemLabels() {
        document.querySelectorAll('.dynamicform_wrapper .item').forEach(function (item, index) {
            const itemTitle = item.querySelector('.card-title');
            if (/Item \d+/.test(itemTitle.textContent)) {
                itemTitle.textContent = 'Item ' + (index + 1);
            }
        });
    }

    // Trigger after adding a new item
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        updateItemLabels();
        toggleCollapse();
        $(item).find('input').val(''); 
    });

    // Trigger after removing an item
    $(".dynamicform_wrapper").on("afterDelete", function(e) {
        updateItemLabels(); 
    });
        
    document.addEventListener("input", function(event) {
        if (event.target.closest('.dynamicform_wrapper .item')) {
            const item = event.target.closest('.item');
            const quantityInput = item.querySelector('input[name*="[quantity]"]');
            const priceInput = item.querySelector('input[name*="[price]"]');
            const amountInput = item.querySelector('input[name*="[item_amount]"]');

            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;

            amountInput.value = total.toFixed(2); 
        }
    });



    updateItemLabels();
JS;

    $this->registerJs($js2);
    ?>
</body>

</html>

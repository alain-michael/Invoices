<?php

use yii\bootstrap5\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use \yii\web\JqueryAsset;

JqueryAsset::register($this);

$this->title = 'Create Invoice';
?>
<style>
    input[name*="item_amount"].is-valid {
        background: var(--bs-secondary-bg) !important;
    }
    .invoice-form {
        margin-top: -50px !important;
    }
</style>
<div class="invoice-form container mt-4 w-75">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

    <div class="row mb-3">
        <div class="col-md-6">
            <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'created_at')->input('date') ?>
        </div>
    </div>

    <h4 class="mt-4">Items</h4>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Item <?= $i + 1 ?></h5>
                    <div>
                        <button type="button" class="add-item btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Add</button>
                        <button type="button" class="remove-item btn btn-danger btn-sm"><i class="glyphicon glyphicon-minus"></i> Remove</button>
                    </div>
                </div>

                <div class="collapse show ">
                    <div class="card-body">
                        <?php if (!$item->isNewRecord): ?>
                            <?= Html::activeHiddenInput($item, "[{$i}]id"); ?>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($item, "[{$i}]name")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($item, "[{$i}]quantity")->textInput(['type' => 'number']) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($item, "[{$i}]price")->textInput(['type' => 'number', 'step' => '0.01']) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($item, "[{$i}]item_amount")->textInput(['readonly'=>true]) ?>
                            </div>
                        </div>

                        <!-- Confirm and Edit buttons -->
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

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
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
        updateItemLabels(); // Update labels after inserting
        $(item).find('input').val(''); // Clear newly added input fields
    });

    // Trigger after removing an item
    $(".dynamicform_wrapper").on("afterDelete", function(e) {
        updateItemLabels(); // Update labels after deleting
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

            amountInput.value = total.toFixed(2); // Update the total amount
        }
    });



    // Initial call to set the labels on page load
    updateItemLabels();
JS;

$this->registerJs($js);
?>
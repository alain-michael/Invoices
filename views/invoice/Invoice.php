<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Invoice $model */
/** @var ActiveForm $form */
?>
<div class="Invoice">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'item') ?>
        <?= $form->field($model, 'item_name') ?>
        <?= $form->field($model, 'item_price') ?>
        <?= $form->field($model, 'quantity') ?>
        <?= $form->field($model, 'created_at') ?>
        <?= $form->field($model, 'customer_name') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>

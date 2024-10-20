<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Add New Item';
?>
<div class="item-create">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal']]); ?>

                    <div class="mb-4">
                        <?= $form->field($model, 'name')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'Enter item name',
                            'autofocus'=> true,
                        ])->label('Item Name') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'description')->textarea([
                            'rows' => 3,
                            'class' => 'form-control',
                            'placeholder' => 'Enter item description'
                        ])->label('Description') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'price')->textInput([
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'placeholder' => 'Enter price'
                        ])->label('Price') ?>
                    </div>

                    <div class="text-end">
                        <?= Html::a('Cancel', ['items'], ['class' => 'btn btn-light me-2']) ?>
                        <?= Html::submitButton('Save Item', ['class' => 'btn btn-primary px-4']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
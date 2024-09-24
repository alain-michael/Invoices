<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Check Payment Status';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-payment-status container mt-5">

    <div class="card shadow border-0 w-50 mx-auto p-4">
        <h2 class="text-center mb-4"><?= Html::encode($this->title) ?></h2>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="mb-4">
                <?= $form->field($model, 'reference_id')->textInput(['class' => 'form-control form-control-md p-3', 'placeholder' => 'Enter a transaction reference ID'])->label(false) ?>
            </div>

            <div class="d-grid">
                <?= Html::submitButton('Check Status', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="text-center mt-3">
            <p class="text-muted">Secure payment processing by MTN.</p>
            <img class="rounded" src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/New-mtn-logo.jpg/800px-New-mtn-logo.jpg" alt="" width="60" height="60">
        </div>
        <div class="text-center mt-3">
            <a href="<?= Url::to(['/']) ?>" class="btn btn-secondary btn-sm">Go to Payment Page</a>
        </div>
    </div>


</div>

<style>
    .site-payment-status .card {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .site-payment-status h2 {
        font-size: 2.2rem;
        font-weight: 600;
        color: #343a40;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #ced4da;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        width: 100%;
        transition: background-color 0.3s, border-color 0.3s;
        border-radius: 10px;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .text-muted {
        font-size: 0.9rem;
    }

    .toast.bg-success {
        background-color: #28a745;
        /* Green for success */
    }

    .toast.bg-danger {
        background-color: #dc3545;
        /* Red for error */
    }
</style>

<?php if (Yii::$app->session->hasFlash('success') || Yii::$app->session->hasFlash('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastBody = document.getElementById('toastBody');
            var toastTitle = document.getElementById('toastTitle');
            var toast = new bootstrap.Toast(document.getElementById('liveToast'));

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                toastBody.textContent = '<?= Yii::$app->session->getFlash('success') ?>';
                toastTitle.textContent = 'Success';
                document.getElementById('liveToast').classList.add('bg-success', 'text-white');
            <?php elseif (Yii::$app->session->hasFlash('error')): ?>
                toastBody.textContent = '<?= Yii::$app->session->getFlash('error') ?>';
                toastTitle.textContent = 'Error';
                document.getElementById('liveToast').classList.add('bg-danger', 'text-white');
            <?php endif; ?>

            toast.show();
        });
    </script>
<?php endif; ?>
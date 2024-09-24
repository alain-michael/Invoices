<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Pay';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-payment container mt-4">

    <div class="card shadow border-1 w-50 mx-auto p-4">
        <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="mb-4">
                <?= $form->field($model, 'number')->textInput(['class' => 'form-control form-control-md', 'placeholder' => 'Enter your mobile number'])->label(false) ?>
            </div>

            <div class="mb-4">
                <?= $form->field($model, 'amount')->textInput(['class' => 'form-control form-control-md', 'placeholder' => 'Enter amount'])->label(false) ?>
            </div>

            <div class="d-grid">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="text-center mt-2">
            <p class="text-muted">Secure payment processing by MTN.</p>
            <img class="rounded rounded-large" src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/New-mtn-logo.jpg/800px-New-mtn-logo.jpg" alt="" width="60" height="60">
        </div>
        <div class="text-center mt-3">
            <a href="<?= Url::to(['payment/check']) ?>" class="btn btn-secondary btn-sm">Check Transaction Status</a>
        </div>    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
                <?php  ?>
            </div>
        </div>
    </div>

</div>

<style>
    .site-payment .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .site-payment h1 {
        font-size: 2.4rem;
        font-weight: bold;
    }

    .form-control {
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        width: 80%;
        margin: 0 auto;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .text-muted {
        font-size: 0.9rem;
    }

    .toast.bg-success {
        background-color: #28a745; /* Green for success */
    }

    .toast.bg-danger {
        background-color: #dc3545; /* Red for error */
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

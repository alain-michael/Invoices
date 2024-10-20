<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css");
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js", ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js", ['position' => \yii\web\View::POS_END]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <?php $this->head() ?>
    <style>
        /* Custom styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #2c3e50;
            color: white;
            transition: all 0.3s;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar .nav-link.active {
            background: #34495e;
            color: white;
            border-left: 4px solid #3498db;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .logo-section {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .content-header {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Card styling improvements */
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: none;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* Button improvements */
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        /* Table improvements */
        .table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }

        /* Form improvements */
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
        <span class="logo-text">Invoice System</span>
    </div>
    <?= Nav::widget([
        'options' => ['class' => 'flex-column'],
        'items' => [
            [
                'label' => '<i class="bi bi-house-door"></i> Dashboard',
                'url' => ['/invoice/index'],
                'encode' => false
            ],
            [
                'label' => '<i class="bi bi-receipt"></i> Invoices',
                'url' => ['/invoice/list'],
                'encode' => false
            ],
            [
                'label' => '<i class="bi bi-plus-circle"></i> Create Invoice',
                'url' => ['/invoice/create'],
                'encode' => false
            ],
            [
                'label' => '<i class="bi bi-box"></i> Items',
                'url' => ['/invoice/items'],
                'encode' => false
            ],
        ]
    ]) ?>
</div>

<!-- Main Content -->
<main class="main-content">
    <div class="content-header">
        <h2 class="mb-0" style="font-size: 1.6rem;"><?= Html::encode($this->title) ?></h1>
    </div>
    <?= Alert::widget() ?>
    <?= $content ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
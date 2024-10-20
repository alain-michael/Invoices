<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\Invoice;
use app\models\InvoiceCharge;
use app\models\InvoiceItems;
use app\models\InvoiceItemsSearch;
use app\models\Items;
use app\models\ModelHelper;
use kartik\mpdf\Pdf;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

class InvoiceController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $invoices = Invoice::find();
        $items = Items::find()->all();
        $totalInvoices = 0;
        $month = date('m');
        $year = date('Y');
        $recentInvoices = $invoices->orderBy(['created_at'  => SORT_DESC])->limit(10)->all();

        $monthlyRevenue = Invoice::find()
            ->where(['strftime("%m", created_at)' => $month, 'strftime("%Y", created_at)' => $year])
            ->sum('total_amount');

        $popularItems = InvoiceItems::find()
            ->select(['name', 'SUM(quantity) AS sales_count', 'price'])
            ->innerJoin('invoice', 'invoice.id = invoice_items.invoice_id')
            ->groupBy('name, price')
            ->orderBy(['sales_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();


        if ($monthlyRevenue === null) {
            $monthlyRevenue = 0;
        }
        foreach ($invoices->all() as $invoice) {
            $totalInvoices += $invoice->total_amount;
        }

        return $this->render('dashboard', ['totalInvoices' => count($invoices->all()), 'totalRevenue' => $totalInvoices, 'invoices' => $invoices, 'totalItems' => count($items), 'popularItems' => $popularItems, 'monthlyRevenue' => $monthlyRevenue, 'recentInvoices' => $recentInvoices]);
    }

    public function findModel($id)
    {
        $invoice = Invoice::findOne($id);
        return $invoice;
    }

    public function findItem($id)
    {
        if (($model = InvoiceItems::findOne($id)) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('The requested item does not exist.');
    }

    public function actionItems()
    {
        $searchModel = new InvoiceItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('items', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        $model = new Invoice();
        $items = [new InvoiceItems()];

        if ($model->load(Yii::$app->request->post())) {
            $items = ModelHelper::createMultiple(InvoiceItems::class);
            Model::loadMultiple($items, Yii::$app->request->post());

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += floatval($item["item_amount"]);
            }
            $model->subtotal = $subtotal;

            $model->additionalCharges = Yii::$app->request->post('charges', []);

            $valid = $model->validate();


            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save(false)) {
                        foreach ($items as $index => $item) {
                            $item->invoice_id = $model->id;
                            $item_choice = Items::find()->where(["id" => Yii::$app->request->post()['InvoiceItems'][$index]['id']])->one();
                            $item->name = $item_choice->name;
                            $item->price = $item_choice->price;
                            $item->load(Yii::$app->request->post());
                            if (!($item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        foreach ($model->additionalCharges as $charge) {
                            $chargeModel = new InvoiceCharge();
                            $chargeModel->invoice_id = $model->id;
                            $chargeModel->name = $charge['name'];
                            $chargeModel->type = $charge['type'];
                            $chargeModel->value = $charge['value'];
                            if (!$chargeModel->save()) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        $valid = Model::validateMultiple($items);

                        if ($valid) {
                            $transaction->commit();
                            return $this->redirect(['view', 'id' => $model->id]);
                        }
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->render('create2', ['model' => $model, 'items' => $items]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('The requested invoice does not exist.');
        }
        $items = $model->invoiceItems;
        $charges = $model->invoiceCharges;

        $html = $this->renderPartial('invoice-pdf', ['model' => $model, 'items' => $items, 'charges' => $charges]);
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('invoice.pdf', ['Attachment' => false]);
        exit();
    }

    public function actionList()
    {
        $invoices = Invoice::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('list', ['invoices' => $invoices]);
    }

    public function actionUpdateItem($id)
    {
        $model = $this->findItem($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Item updated successfully.');
            return $this->redirect(['items']);
        }

        return $this->render('update-item', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $items = $model->invoiceItems;


        if ($model->load(Yii::$app->request->post())) {
            $oldIDs = ArrayHelper::map($items, 'id', 'id');
            $items = ModelHelper::createMultiple(InvoiceItems::class, $items);
            Model::loadMultiple($items, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($items, 'id', 'id')));

            $valid = $model->validate();
            $valid = Model::validateMultiple($items) && $valid;

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            InvoiceItems::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($items as $item) {
                            $item->invoice_id = $model->id;
                            if (! ($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'items' => (empty($items)) ? [new InvoiceItems()] : $items
        ]);
    }

    public function actionDeleteInvoice($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Invoice deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to delete invoice.');
        }
        return $this->redirect(['list']);
    }

    public function actionAddItem()
    {
        $model = new Items();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Item created successfully.');
            return $this->redirect(['items']);
        }
        if ($model->errors) {
            dump($model);
            die();
        }

        return $this->render('add-item', [
            'model' => $model,
        ]);
    }
}

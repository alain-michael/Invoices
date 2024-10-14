<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\models\Invoice;
use app\models\InvoiceCharge;
use app\models\InvoiceItems;
use app\models\ModelHelper;
use kartik\mpdf\Pdf;
use yii\base\Model;

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
        $model = new Invoice();
        return $this->render('invoice', ['model' => $model]);
    }

    public function findModel($id)
    {
        $invoice = Invoice::findOne($id);
        return $invoice;
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
                        foreach ($items as $item) {
                            $item->invoice_id = $model->id;
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
        if($model === null) {
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
}
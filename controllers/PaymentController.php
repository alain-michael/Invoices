<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Transactions;
use Ramsey\Uuid\Uuid;

/**
 * Class APIClient
 *
 * This class handles communication with the MTN MoMo API, including obtaining
 * access tokens and making requests to various endpoints.
 */
class APIClient
{
    private $client;


    /**
     * APIClient constructor.
     *
     * Initializes a new instance of the APIClient and its underlying HTTP client.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Retrieves an access token from the MTN MoMo API.
     *
     * This method sends a POST request to the token endpoint to obtain an access
     * token using Basic Authentication. The access token is stored in the session
     * along with its expiry time.
     *
     * @return string The access token retrieved from the API.
     * @throws \yii\web\HttpException If the API response is not successful.
     */
    public function getAccessToken()
    {
        $credentials = base64_encode($_ENV['API_USER'] . ':' . $_ENV['API_PASS']);
        $response = $this->client->post('https://sandbox.momodeveloper.mtn.com/collection/token/', headers: [
            'Authorization' => 'Basic ' . $credentials,
            'Ocp-Apim-Subscription-Key' => $_ENV['SUBSCRIPTION_KEY'],
        ]);

        $response = $response->send();

        if ($response->isOk) {
            Yii::$app->session->set('access_token', $response->data['access_token']);
            Yii::$app->session->set('token_expiry', time() + 3600);
        } else {
            throw new \yii\web\HttpException(400, 'Error: Invalid response');
        }
        return $response->data['access_token'];
    }

    /**
     * Creates a new request to the MTN MoMo API.
     *
     * This method prepares an HTTP request with the specified method, endpoint,
     * data, and headers. It ensures a valid access token is used, obtaining a
     * new token if necessary.
     *
     * @param string $method The HTTP method (e.g., 'GET', 'POST').
     * @param string $endpoint The API endpoint to which the request is sent.
     * @param array $data Optional. The data to send with the request (for POST requests).
     * @param array $headers Optional. Additional headers to include in the request.
     * @return \yii\httpclient\Request The prepared request object.
     */
    public function createRequest($method, $endpoint, $data = [], $headers = [])
    {
        $access = Yii::$app->session->get('access_token');
        $expiry = Yii::$app->session->get('token_expiry');

        if ($access && $expiry && $expiry > time()) {
            $token = $access;
        } else {
            $token = $this->getAccessToken();
        }

        $request =  $this->client->createRequest()
            ->setMethod($method)
            ->setUrl('https://sandbox.momodeveloper.mtn.com/' . $endpoint)
            ->addHeaders(array_merge([
                'Authorization' => 'Bearer ' . $token,
                'Ocp-Apim-Subscription-Key' => $_ENV['SUBSCRIPTION_KEY'],
                'X-Target-Environment' => 'sandbox',
            ], $headers));

        if ($method === 'POST') {
            $request->setData($data);
        }

        return $request;
    }
}

class PaymentController extends Controller
{
    /**
     * {@inheritdoc}
     */

    public $api_client;

    public function __construct($id, $module, $config = [])
    {
        $this->api_client = new APIClient();
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Displays the payment request form and handles payment requests.
     *
     * This action initializes a new Transactions model, processes the request
     * for payment when the form is submitted, and sends a payment request
     * to the API. It sets session flash messages based on the success or
     * failure of the payment request.
     *
     * @return string The rendered view for the payment request.
     */
    public function actionIndex()
    {
        $model = new Transactions();
        $uuid = Uuid::uuid4()->toString();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $response = $this->api_client->createRequest(
                'POST',
                'collection/v1_0/requesttopay',
                [
                    'amount' => $model->amount,
                    'currency' => 'EUR',
                    'externalId' => (string)$model->id,
                    'payer' => [
                        'partyIdType' => 'MSISDN',
                        'partyId' => "$model->number"
                    ],
                    "payerMessage" => "Payment of $model->amount",
                    "payeeNote" => "Payment of $model->amount",
                ],
                [
                    'Content-Type' => 'application/json',
                    'X-Reference-Id' => $uuid,
                    'X-Callback-Url' => $_ENV['CALLBACK_URL'],
                ]
            )->setFormat(Client::FORMAT_JSON)->send();

            if ($response->isOk) {
                $model->reference_id = $uuid;
                $model->save();
                Yii::$app->session->setFlash('success', 'Payment request sent successfully with ID ' . $model->id . '.');
            } else {
                Yii::$app->session->setFlash('error', 'Payment request failed.');
            }
        }

        return $this->render('payment', [
            'model' => $model
        ]);
    }

    /**
     * Checks the status of a payment based on the reference ID.
     *
     * This action retrieves the transaction associated with the provided 
     * reference ID and sets flash messages based on the transaction status. 
     * It handles both successful and failed payment statuses as well as 
     * pending requests.
     *
     * @return string The rendered view for checking payment status.
     */
    public function actionCheck()
    {
        $model = new Transactions();
        if (Yii::$app->request->post()) {
            $transaction = Transactions::findOne(['id' => Yii::$app->request->post()['Transactions']['reference_id']]);

            if ($transaction) {
                $response = $this->api_client->createRequest(
                    'GET',
                    'collection/v1_0/requesttopay/' . $transaction->reference_id
                )->send();
                if ($response->isOk) {
                    if ($response->data['status'] === 'SUCCESS') {
                        Yii::$app->session->setFlash('success', 'Payment successful.');
                    } elseif ($response->data['status'] === 'FAILED') {
                        Yii::$app->session->setFlash('error', 'Payment failed with reason: ' . ucwords(strtolower(str_replace('_', ' ', $response->data['reason']))) . '.');
                    } else {
                        Yii::$app->session->setFlash('info', 'Payment pending.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Payment request failed.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Transaction not found.');
            }
            return $this->render('check', [
                'model' => $model
            ]);
        }
        return $this->render('check', [
            'model' => $model
        ]);
    }

    /**
     * Handles callback from the payment gateway.
     *
     * This action processes the callback from the payment API, and updates 
     * the transaction status based on the received status.
     * It can handle both successful and failed transactions.
     *
     * @return string The rendered view for the callback response.
     */
    public function actionCallback()
    {
        $status = Yii::$app->request->post('status');
        $transaction = Transactions::findOne(['id' => Yii::$app->request->post('externalId')]);
        if ($status === 'FAILED') {
            $transaction->status = 'FAILED';
            $transaction->reason = Yii::$app->request->post('reason');
        } else {
            $transaction->status = 'SUCCESS';
        }
        $transaction->save();
    }
}

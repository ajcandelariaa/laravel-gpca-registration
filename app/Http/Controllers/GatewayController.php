<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;


class GatewayController extends Controller
{
    public $orderId = 64;
    public $transactionId = 66;

    public function getSessionId()
    {
        $client = new Client();

        $response = $client->request('POST', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/session', [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if ($data['result'] == "SUCCESS") {
            Session::put('sessionId', $data['session']['id']);
            Session::put('updateStatus', $data['session']['updateStatus']);
            return redirect('/updateSession');
        }
    }

    public function updateSession()
    {
        if (Session::has('sessionId') && Session::has('updateStatus')) {
            if (Session::get('updateStatus') == "NO_UPDATE") {
                $sessionId = Session::get('sessionId');
                $client = new Client();

                $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/session/' . $sessionId, [
                    'auth' => [
                        'merchant.TEST900755',
                        '3b41414705a08d0fa159a77316aba3b3'
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        "order" => [
                            'amount' => '100.00',
                            'currency' => 'USD',
                        ],
                    ]
                ]);

                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if ($data['session']['updateStatus'] == "SUCCESS") {
                    Session::put('updateStatus', $data['session']['updateStatus']);
                    Session::put('order', $data['order']);
                    return redirect('/cardDetails');
                }
            }
        }
    }

    public function initializeAPIConfiguration()
    {
        if (Session::has('sessionId') && Session::has('updateStatus') && Session::has('order')) {
            if (Session::get('updateStatus') == "SUCCESS") {
                return view('intialize_api_config');
            }
        }
    }

    public function cardDetails()
    {
        if (Session::has('sessionId') && Session::has('updateStatus') && Session::has('order')) {
            if (Session::get('updateStatus') == "SUCCESS") {
                return view('card_details');
            }
        }
    }

    public function retrieveSession()
    {
        $client = new Client();
        $sessionId = Session::get('sessionId');
        $response = $client->request('GET', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/session/' . $sessionId, [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        Session::put('sourceOfFunds', $data['sourceOfFunds']);
        return redirect('/getToken');
    }

    public function getToken()
    {
        $client = new Client();
        $sessionId = Session::get('sessionId');
        $response = $client->request('POST', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/token', [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "session" => [
                    'id' => $sessionId,
                ],
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        if ($data['result'] == "SUCCESS" && $data['status'] == "VALID") {
            Session::put('token', $data['token']);
            Session::put('tokenResult', $data['result']);
            Session::put('tokenStatus', $data['status']);
            return redirect('/initiateAuthentication');
        }
    }

    public function initiateAuthentication()
    {
        $client = new Client();
        $sessionId = Session::get('sessionId');
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/order/' . $this->orderId . '/transaction/' . $this->transactionId, [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "session" => [
                    'id' => $sessionId,
                ],
                "authentication" => [
                    "acceptVersions" => "3DS1,3DS2",
                    "channel" => "PAYER_BROWSER",
                    "purpose" => "PAYMENT_TRANSACTION"
                ],
                "correlationId" => "test",
                "apiOperation" => "INITIATE_AUTHENTICATION"
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        return redirect('/initiateAuthenticationPayerRequest');
    }

    public function initiateAuthenticationPayerRequest()
    {
        $client = new Client();
        $sessionId = Session::get('sessionId');
        $token = Session::get('token');
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/order/' . $this->orderId . '/transaction/' . $this->transactionId, [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "session" => [
                    'id' => $sessionId,
                ],
                "authentication" => [
                    "redirectResponseUrl" => "http://127.0.0.1:8000/authentcatedDone"
                ],
                "correlationId" => "test",
                "device" =>  [
                    "browser" =>  "MOZILLA",
                    "browserDetails" =>  [
                        "3DSecureChallengeWindowSize" =>  "FULL_SCREEN",
                        "acceptHeaders" =>  "application/json",
                        "colorDepth" =>  24,
                        "javaEnabled" =>  true,
                        "language" =>  "en-US",
                        "screenHeight" =>  1640,
                        "screenWidth" =>  1480,
                        "timeZone" =>  273
                    ],
                    "ipAddress" =>  "127.0.0.1"
                ],
                "apiOperation" => "AUTHENTICATE_PAYER",
            ],
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        dd($token);
        $htmlCode = $data['authentication']['redirect']['html'];
        return view('testOtp', [
            'htmlCode' => $htmlCode,
        ]);
    }

    public function authentcatedDone()
    {
        return redirect('/payNow');
    }

    public function payNow()
    {
        $client = new Client();
        $sessionId = Session::get('sessionId');
        $sourceOfFunds = Session::get('sourceOfFunds');
        $token = Session::get('token');
        dd($token);
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/order/' . $this->orderId . '/transaction/' . $this->transactionId, [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'apiOperation' => "PAY",
                "order" => [
                    "amount" => '100.0',
                    "currency" => 'USD',
                ],
                "session" => [
                    "id" => $sessionId,
                ],
                "sourceOfFunds" => [
                    "token" => $token,
                ],
            ]
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        dd($data);
    }
}

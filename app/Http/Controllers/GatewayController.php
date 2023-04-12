<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Svg\Tag\Rect;

class GatewayController extends Controller
{
    public function getSessionId(Request $request)
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
        // retrieve the response body as a string
        $body = $response->getBody()->getContents();

        // parse the JSON response into a PHP array
        $data = json_decode($body, true);
        $request->session()->put('sessionId', $data['session']['id']);
        return redirect('/updateSession');
    }

    public function updateSession(Request $request)
    {
        $client = new Client();
        $sessionId = session('sessionId');
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
        // retrieve the response body as a string
        $body = $response->getBody()->getContents();

        // parse the JSON response into a PHP array
        $data = json_decode($body, true);
        $request->session()->put('sessionId', $data['session']['id']);
        return redirect('/cardDetails');
    }
    public function cardDetails()
    {
        return view('card_details');
    }

    public function getToken(Request $request)
    {
        $client = new Client();
        $sessionId = session('sessionId');
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
                "sourceOfFunds" =>  [
                    "provided" =>  [
                        "card" =>  [
                            "expiry" =>  [
                                "month" =>  "05",
                                "year" =>  "23"
                            ],
                            "number" =>  "4242424242424242"
                        ]
                    ],
                    "type" =>  "CARD"
                ]
            ]
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        $request->session()->put('token', $data['token']);
        $request->session()->put('repositoryId', $data['repositoryId']);
        return redirect('/payNow');
    }

    public function authorizePayment(Request $request)
    {
        $client = new Client();
        $sessionId = session('sessionId');
        $token = session('token');
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/order/10/transaction/11', [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'apiOperation' => "AUTHORIZE",
                "order" => [
                    'amount' => '100.00',
                    'currency' => 'USD',
                ],
                "session" => [
                    "id" => $sessionId,
                ],
                "sourceOfFunds" => [
                    "token" => $token,
                ]
            ]
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        dd($data);
    }

    public function payNow(Request $request)
    {
        $client = new Client();
        $sessionId = session('sessionId');
        $token = session('token');
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/order/14/transaction/15', [
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
                    'amount' => '100.00',
                    'currency' => 'USD',
                ],
                "session" => [
                    "id" => $sessionId,
                ],
                "sourceOfFunds" => [
                    "token" => $token,
                ]
            ]
        ]);
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        dd($data);
    }
}

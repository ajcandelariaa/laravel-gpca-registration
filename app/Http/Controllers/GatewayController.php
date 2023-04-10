<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;


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
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755/session/'.$sessionId, [
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
    public function cardDetails(){
        return view('card_details');
    }

    public function payNow(Request $request){
        $client = new Client();
        $sessionId = session('sessionId');
        $response = $client->request('PUT', 'https://ap-gateway.mastercard.com/api/rest/version/70/merchant/TEST900755//order/1/transaction/2', [
            'auth' => [
                'merchant.TEST900755',
                '3b41414705a08d0fa159a77316aba3b3'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "apiOperation" => "PAY",
                "order" => [
                    'amount' => '100.00',
                    'currency' => 'USD',
                ],
                "session" => [
                    'id' => $sessionId,
                ],
                "sourceOfFunds" => [
                    'token' => '100.00',
                    'currency' => 'USD',
                ],
            ]
        ]);
        // retrieve the response body as a string
        $body = $response->getBody()->getContents();

        // parse the JSON response into a PHP array
        $data = json_decode($body, true);
        dd($data);
    }
}

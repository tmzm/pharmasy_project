<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * @throws GuzzleException
     */
    public function notify($title, $body, $key)
    {
        $serverKey = env('KEY');

        $client = new Client([
            'verify' => false,
            'headers' => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $message = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'to' => $key,
            'message_id' => uniqid()
        ];

            $client->post('https://fcm.googleapis.com/fcm/send', [
                'json' => $message,
            ]);
    }
}

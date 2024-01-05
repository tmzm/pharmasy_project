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
    public function notify($title, $body, $key): void
    {
        $serverKey = 'AAAAiAS4L9g:APA91bE8IABq-5G5DlPh7tSUEU1MmiL_PonnTnwtLjqUh8LE2mBdQyWiG3D4Ec3OT0c6paEHu4h24vcx5E-5IfsyG3MIWLHMgTvaqN2Rn3FFR0SwrCyP0ielNIuFE5FrV6cjKXSJkgWC';

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

        try {
            $client->post('https://fcm.googleapis.com/fcm/send', [
                'json' => $message,
            ]);
        }catch (\Exception $e){

        }
    }
}

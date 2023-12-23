<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\Notification;

class NotificationController extends Controller
{
    use ApiResponse;

    public function notify($title,$body,$device_key)
    {
        $url = 'https://fcw.googleapis.com/fcw/send';
        $serverKey = env('serverKey');
        $message=[
          'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
          'status' => 'done'
        ];

        $data = [
            'registration_ids' => [$device_key],
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default'
            ],
            'data' => $message,
            'priority' => 'high'
        ];

        $encodeData = json_encode($data);
        $header = [
          'Authentication:key=' . $serverKey,
          'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$encodeData);

        $result = curl_exec($ch);

        if($result === false){
            return $this->apiResponse(500,'error');
        }

        curl_close($ch);

    }

    /**
     * create the notification and send it
     * @param Request $request
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $this->notify($request->title,$request->body,$user->device_key);
    }
}

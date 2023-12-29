<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function notify(Request $request): \Illuminate\Http\Response
    {
        $title = $request['title'];
        $body = $request['body'];
        $device_key = $request['key'];

        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = "AAAAiAS4L9g:APA91bE8IABq-5G5DlPh7tSUEU1MmiL_PonnTnwtLjqUh8LE2mBdQyWiG3D4Ec3OT0c6paEHu4h24vcx5E-5IfsyG3MIWLHMgTvaqN2Rn3FFR0SwrCyP0ielNIuFE5FrV6cjKXSJkgWC";
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

        return $this->apiResponse(200,'ok',$result);
    }

    /**
     * create the notification and send it
     * @param Request $request
     */
    public function create(Request $request)
    {
        $this->notify($request['title'],$request['body'],$request->user()->device_key);
    }
}

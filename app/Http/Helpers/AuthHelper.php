<?php

namespace App\Http\Helpers;

use App\Http\Controllers\NotificationController;
use App\Models\Warehouse;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;

trait AuthHelper
{
    public function register_user($request): array
    {
        $data = $request->validated();

        $user = self::create_user($data);

        $token = $user->createToken('UserToken')->accessToken;

        if($data['role'] == 'warehouse_owner'){
            $image = self::save_image_to_public_directory($request);

            if($image !== false)
                $data['image'] = $image;

            $warehouse = self::create_warehouse($data,$user->id);

            self::ok(['user'=>$user,'warehouse'=>$warehouse],$token);
        }

        self::ok($user,$token);
    }

    public function login_user($request): void
    {
        $data = $request->validated();

        auth()->attempt($data) ? self::ok(auth()->user(), auth()->user()->createToken('UserToken')->accessToken) : self::unAuth();
    }

    public function update_user($request): void
    {
        $data = $request->validated();

        $user = $request->user();

        $user->update($data);

        if($user->role == 'warehouse_owner'){
            $warehouse = Warehouse::firstWhere('user_id',$user->id);

            $image = self::save_image_to_public_directory($request);

            if($image !== false)
                $data['image'] = $image;

            $data['name'] = $data['warehouseName'];

            $dataForUser = Arr::except($data, 'warehouseName');

            $warehouse->update($dataForUser);

            self::ok($warehouse);
        }

        self::ok($user);
    }

    public function logout_user($request): void
    {
        $request->user()->token()->revoke() ? self::ok() : self::unHandledError();
    }

    public function show_user_details($request): void
    {
        $request->user()->role == 'user' ? self::ok($request->user()) : self::ok(Warehouse::firstWhere('user_id', $request->user()->id));
    }

    /**
     * @throws GuzzleException
     */
    public function send_order_notification_to_user($request, $user): void
    {
        if(isset($request['status']))
            (new NotificationController)->notify(
                'the order has updated',
                'the order new status is: ' . $request['status'],
                $user->device_key
            );
        if(isset($request['payment_status']))
            if($request['payment_status']) $paid = 'paid'; else $paid = 'not paid';
        (new NotificationController)->notify(
            'the order has updated',
            'the order set to: ' . $paid,
            $user->device_key
        );
    }

    public function edit_fcm_token($request): void
    {
        $user = $request->user();

        isset($request['fcm_token']) ? $user->device_key = $request['fcm_token'] : self::unHandledError();

        self::ok();
    }
}

<?php

namespace App\Http\Helpers;

use App\Models\Warehouse;
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
}

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

            return ['user'=>$user,'warehouse'=>$warehouse,'token'=>$token];
        }

        return ['user'=>$user,'token'=>$token];
    }

    public function login_user($request): mixed
    {
        $data = $request->validated();

        if (auth()->attempt($data))
            return auth()->user()->createToken('UserToken')->accessToken;

        return false;
    }

    public function update_user($request): mixed
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

            return $warehouse;
        }

        return $user;
    }
}

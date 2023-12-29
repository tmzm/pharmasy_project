<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @param CreateUserRequest $request
     * @return Response
     */
    public function create(CreateUserRequest $request): Response
    {
        $user = self::register_user($request);

        return self::apiResponse(200,ReturnMessages::Ok->value,$user,$user->token ?? null);
    }

    /**
     * @param StoreUserRequest $request
     * @return Response
     */
    public function store(StoreUserRequest $request): Response
    {
        $token = self::login_user($request);

        if($token)
            return self::apiResponse(200,ReturnMessages::Ok->value,auth()->user(),$token);

        return self::apiResponse(401,ReturnMessages::UnAuth->value);
    }

    /**
     * @param UpdateUserRequest $request
     * @return Response
     */
    public function update(UpdateUserRequest $request): Response
    {
        $user = self::update_user($request);

        return self::apiResponse(200,ReturnMessages::Ok->value,$user);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request): Response
    {
        if($request->user()->token()->revoke())
            return self::apiResponse(200,ReturnMessages::Ok->value);

        return self::apiResponse(500,ReturnMessages::Error->value);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        if($request->user()->role == 'user')
             return self::apiResponse(200,ReturnMessages::Ok->value,$request->user());

        return self::apiResponse(200,ReturnMessages::Ok->value,['user'=>$request->user(),'warehouse'=>Warehouse::firstWhere('user_id',$request->user()->id)]);
    }
}

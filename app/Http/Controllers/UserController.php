<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param CreateUserRequest $request
     */
    public function create(CreateUserRequest $request)
    {
        self::register_user($request);
    }

    /**
     * @param StoreUserRequest $request
     */
    public function store(StoreUserRequest $request)
    {
        self::login_user($request);
    }

    /**
     * @param UpdateUserRequest $request
     */
    public function update(UpdateUserRequest $request)
    {
        self::update_user($request);
    }

    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        $request->user()->token()->revoke() ? self::ok() : self::unHandledError();
    }

    /**
     * @param Request $request
     */
    public function show(Request $request)
    {
        $request->user()->role == 'user' ? self::ok($request->user()) : self::ok(['user' => $request->user(), 'warehouse' => Warehouse::firstWhere('user_id', $request->user()->id)]);
    }
}

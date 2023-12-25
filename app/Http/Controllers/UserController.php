<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * @return Response
     * @throws ValidationException
     */
    public function create(): Response
    {
        $validator = validator(request()->all(), [
            'name' => 'required|min:3|max:50',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|min:8|max:30',
            'role' => 'required'
        ]);

        if($validator->fails())
            return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

        $data = $validator->validated();

        if($data['role'] == 'warehouse_owner'){
            $validatorWarehouseOwner = validator(request()->all(), [
                'warehouseName' => 'required|min:3|max:50',
                'image' => ['image','mimes:jpg,jpeg,png,svg'],
                'location' => 'required|min:10|max:50',
            ]);

            if($validatorWarehouseOwner->fails())
                return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validatorWarehouseOwner->errors());

            $d = $validatorWarehouseOwner->validated();

            $user = User::create([
                'name' => $data['name'],
                'phone_number' => $data['phone_number'],
                'password' => bcrypt($data['password']),
                'role' => $data['role']
            ]);

            try{
                if (request()->hasfile('image')) {
                    $d['image'] = '/storage/' . substr(request()->file('image')->store('public/warehouses') , 7);
                }
            }catch(Exception $e){
                return $this->apiResponse(500,ReturnMessages::Error->value,null,null,$e);
            }

            $warehouse = Warehouse::create([
                'name' => $d['warehouseName'],
                'location' => $d['location'],
                'image' => $d['image'],
                'user_id' => $user->id
            ]);

            $token = $user->createToken('UserToken')->accessToken;

            return $this->apiResponse(200,ReturnMessages::Ok->value,['user'=>$user,'warehouse'=>$warehouse],$token);

        }

        $user = User::create([
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'password' => bcrypt($data['password']),
            'role' => $data['role']
        ]);

        $token = $user->createToken('UserToken')->accessToken;

        return $this->apiResponse(200,ReturnMessages::Ok->value,$user,$token);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request): Response
    {
        $validator = validator(request()->all(), [
            'phone_number' => 'required',
            'password' => 'required|min:8|max:30',
            'role' => 'required'
        ]);

        if($validator->fails())
            return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

        $data = $validator->validated();

        $user = User::firstWhere('role','=',$data['role']);

        if(!$user)
            return $this->apiResponse(401, ReturnMessages::UnAuth->value);


        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('UserToken')->accessToken;
            return $this->apiResponse(200,ReturnMessages::Ok->value,auth()->user(),$token);
        } else
            return $this->apiResponse(401,ReturnMessages::UnAuth->value);

        return $this->apiResponse(200,'ok',auth()->user());
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request): Response
    {
        $validator = validator(request()->all(), [
            'name' => 'min:3|max:50',
        ]);

        if($validator->fails())
            return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

        $data = $validator->validated();

        $user = $request->user();

        if($user->role == 'warehouse_owner'){
            $warehouse = Warehouse::firstWhere('user_id',$user->id);

            $validatorWarehouseOwner = validator(request()->all(), [
                'warehouseName' => 'min:3|max:50',
                'location' => 'min:10|max:50',
                'image' => ['image','mimes:jpg,jpeg,png,svg'],
            ]);

            if($validatorWarehouseOwner->fails())
                return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validatorWarehouseOwner->errors());

            $d = $validatorWarehouseOwner->validated();

            try{
                if (request()->hasfile('image')) {
                    $d['image'] = '/storage/' . substr(request()->file('image')->store('public/warehouses') , 7);
                }
            }catch(Exception $e){
                return $this->apiResponse(500,ReturnMessages::Error->value,null,null,$e);
            }

            $user->update($data);

            $warehouse->update($d);

            return $this->apiResponse(200,ReturnMessages::Ok->value,['user'=>$user,'warehouse'=>$warehouse]);

        }

        $user->update($data);

        return $this->apiResponse(200,ReturnMessages::Ok->value,$user);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request): Response
    {
        $request->user()->token()->revoke();

        return $this->apiResponse(200,ReturnMessages::Ok->value);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        if($request->user()->role == 'user')
             return $this->apiResponse(200,ReturnMessages::Ok->value,$request->user());

        return $this->apiResponse(200,ReturnMessages::Ok->value,['user'=>$request->user(),'warehouse'=>Warehouse::firstWhere('user_id',$request->user()->id)]);
    }
}

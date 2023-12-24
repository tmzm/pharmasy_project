<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(): Response
    {
        return $this->apiResponse(200,ReturnMessages::Ok->value,Warehouse::latest()->get());
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $warehouse_id
     * @return Response
     */
    public function show(Request $request,$warehouse_id): Response
    {
        if($request->user()->role == 'warehouse_owner')
            $warehouse = Warehouse::firstWhere('user_id',$request->user()->id);
        else
            $warehouse = Warehouse::find($warehouse_id);

        if($warehouse)
            return $this->apiResponse(200,ReturnMessages::Ok->value,$warehouse);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}

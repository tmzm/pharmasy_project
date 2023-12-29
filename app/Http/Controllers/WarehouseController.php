<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Warehouse;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{/**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(): Response
    {
        return self::apiResponse(200,ReturnMessages::Ok->value,Warehouse::latest()->get());
    }

    /**
     * Display the specified resource.
     * @param $warehouse_id
     * @return Response
     */
    public function show($warehouse_id): Response
    {
        $warehouse = Warehouse::find($warehouse_id);

        return Warehouse::find($warehouse_id) ? self::apiResponse(200, ReturnMessages::Ok->value, $warehouse) : self::apiResponse(404, ReturnMessages::NotFound->value);
    }
}

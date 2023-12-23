<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application as Application2;
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
        return $this->apiResponse(200,'ok',Warehouse::get());
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $warehouse_id
     * @return Response
     */
    public function show(Request $request,$warehouse_id): Response
    {
        $warehouse = Warehouse::find($warehouse_id);

        if($warehouse)
            return $this->apiResponse(200,'ok',$warehouse);

        return $this->apiResponse(404,'Not found');
    }
}

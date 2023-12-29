<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Warehouse;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{/**
     * Display a listing of the resource.
     */
    public function index()
    {
        self::ok(Warehouse::latest()->get());
    }

    /**
     * Display the specified resource.
     * @param $warehouse_id
     */
    public function show($warehouse_id)
    {
        $warehouse = Warehouse::find($warehouse_id);

        $warehouse ? self::ok($warehouse) : self::notFound();
    }
}

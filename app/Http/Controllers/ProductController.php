<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        self::filter_products(request(['search', 'category', 'warehouse_id']),$request);
    }


    /**
     * @param CreateProductRequest $request
     */
    public function create(CreateProductRequest $request)
    {
        self::create_product($request);
    }


    /**
     * @param $product_id
     */
    public function show($product_id)
    {
        self::get_product_by_id($product_id);
    }


    /**
     * @param UpdateProductRequest $request
     * @param $product_id
     */
    public function update(UpdateProductRequest $request, $product_id)
    {
        self::update_product($request, $product_id);
    }


    /**
     * @param Request $request
     * @param $product_id
     * @throws GuzzleException
     */
    public function destroy(Request $request, $product_id)
    {
        self::delete_product($request,$product_id);
    }
}

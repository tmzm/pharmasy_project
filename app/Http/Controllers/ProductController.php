<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

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
        $product = Product::find($product_id);

        $product ? self::ok($product) : self::notFound();
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
     */
    public function destroy(Request $request, $product_id)
    {
        self::delete_product($request,$product_id);
    }
}

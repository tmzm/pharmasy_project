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
     * @return Response
     */
    public function index(Request $request): Response
    {
        $products = self::filter_products(request(['search', 'category', 'warehouse_id']),$request);

        if(count($products))
            return self::apiResponse(200,ReturnMessages::Ok->value,$products);

        return self::apiResponse(404,ReturnMessages::NotFound->value);
    }


    /**
     * @param CreateProductRequest $request
     * @return Response
     */
    public function create(CreateProductRequest $request): Response
    {
        return self::apiResponse(200, ReturnMessages::Ok->value, self::create_product($request));
    }


    /**
     * @param $product_id
     * @return Response
     */
    public function show($product_id): Response
    {
        $product = Product::find($product_id);

        if($product)
            return self::apiResponse(200,ReturnMessages::Ok->value,$product);

        return self::apiResponse(404,ReturnMessages::NotFound->value);
    }


    /**
     * @param UpdateProductRequest $request
     * @param $product_id
     * @return Response
     * @throws ValidationException
     */
    public function update(UpdateProductRequest $request, $product_id): Response
    {
        $product = Product::byOwnerAndProductId($product_id,$request->user()->id)->first();

        if($product)
            return self::apiResponse(200, ReturnMessages::Ok->value, self::update_product($request,$product));

        return self::apiResponse(404,ReturnMessages::NotFound->value);
    }


    /**
     * @param Request $request
     * @param $product_id
     * @return Response
     */
    public function destroy(Request $request, $product_id): Response
    {
        $product = Product::byOwnerAndProductId($request->user()->id,$product_id);

        if($product) {
            $product->delete();

            return self::apiResponse(200, ReturnMessages::Ok->value);
        }

        return self::apiResponse(404,ReturnMessages::NotFound->value);
    }
}

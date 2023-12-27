<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $products = $this->filter_products(request(['search', 'category', 'warehouse_id']),$request);

        if(count($products))
            return $this->apiResponse(200,ReturnMessages::Ok->value,$products);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }


    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function create(Request $request): Response
    {
        $validator = validator($request->all(),[
            'scientific_name' => 'required|min:10|max:50',
            'commercial_name' => 'required|min:10|max:50',
            'company_name' => 'required|min:10|max:50',
            'quantity' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'warehouse_id' => '',
            'expiration' => 'required|date',
            'image' => ['required','image','mimes:jpg,jpeg,png,svg']
        ]);

        if($validator->fails())
            return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

        $data = $validator->validated();

        $data['warehouse_id'] = Warehouse::where('user_id',$request->user()->id)->first()->id;

        $image = $this->save_image_to_public_directory($request);

        if($image !== false)
            $data['image'] = $image;

        $product = Product::create($data);

        return $this->apiResponse(200, ReturnMessages::Ok->value, $product);
    }


    /**
     * @param $product_id
     * @return Response
     */
    public function show($product_id): Response
    {
        $product = Product::find($product_id);

        if($product)
            return $this->apiResponse(200,ReturnMessages::Ok->value,$product);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }


    /**
     * @param Request $request
     * @param $product_id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $product_id): Response
    {
        $product = $this->get_only_warehouse_product($product_id,$request->user()->id);

        if($product) {
            $validator = validator($request->all(),[
                'scientific_name' => 'min:8|max:50',
                'commercial_name' => 'min:8|max:50',
                'company_name' => 'min:8|max:50',
                'quantity' => 'number',
                'price' => 'number',
                'category_id' => 'number',
                'expiration' => 'date',
                'image' => ['image','mimes:jpg,jpeg,png,svg']
            ]);

            if($validator->fails())
                return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

            $data = $validator->validated();

            $image = $this->save_image_to_public_directory($request);

            if($image !== false)
                $data['image'] = $image;

            $product->update($data);

            return $this->apiResponse(200, ReturnMessages::Ok->value, $product);
        }
        return $this->apiResponse(404,ReturnMessages::NotFound->value);

    }


    /**
     * @param Request $request
     * @param $product_id
     * @return Response
     */
    public function destroy(Request $request, $product_id): Response
    {
        $product = $this->get_only_warehouse_product($product_id,$request->user()->id);

        if($product) {
            $product->delete();

            return $this->apiResponse(200, ReturnMessages::Ok->value);
        }

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Product;
use App\Models\Warehouse;
use Exception;
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
        $filters = request(['search', 'category', 'warehouse_id']);

        if ($request->user()->role == 'user')
            $warehouse_id = request('warehouse_id');
        else {
            $warehouse_id = Warehouse::firstWhere('user_id',$request->user()->id)->id;
        }

        $products = Product::filter($filters,$warehouse_id)->latest()->get();

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

        try{
            if ($request->hasfile('image')) {
                $image = $request->file('image');
                $imageName = time().'_'.$request->file('image')->getClientOriginalExtension();
                copy($image, public_path('images/' . $imageName));
                $data['image'] = '/images/' .  $imageName;
            }
        }catch(Exception $e){
            return $this->apiResponse(500,ReturnMessages::Error->value,null,null,$e);
        }

        $product = Product::create($data);

        return $this->apiResponse(200, ReturnMessages::Ok->value, $product);
    }


    /**
     * @param Request $request
     * @param $product_id
     * @return Response
     */
    public function show(Request $request, $product_id): Response
    {
        $product = Product::find($product_id);

        if($request->user()->role == 'warehouse_owner')
            $product = $product->whereHas('warehouse', fn ($query)

            => $query->where('user_id',$request->user()->id)

            )->first();

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
        $product = Product::whereHas('warehouse',function ($query) use ($product_id,$request){
            $query->where('user_id',$request->user()->id);
        })->firstWhere('id',$product_id);

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

            try{
                if ($request->hasfile('image')) {
                    $image = $request->file('image');
                    $imageName = time().'_'.$request->file('image')->getClientOriginalExtension();
                    copy($image, public_path('images/' . $imageName));
                    $data['image'] = '/images/' .  $imageName;
                }
            }catch(Exception $e){
                return $this->apiResponse(500,ReturnMessages::Error->value,null,null,$e);
            }

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
        $product = Product::whereHas('warehouse',function ($query) use ($product_id,$request){
                $query->where('user_id',$request->user()->id);
            })->firstWhere('id',$product_id);

        if($product) {
            $product->delete();

            return $this->apiResponse(200, ReturnMessages::Ok->value);
        }

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}

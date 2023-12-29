<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $favorites = Favorite::latest()->where('user_id',$request->user()->id)->get();

        return count($favorites) ? $this->apiResponse(200, ReturnMessages::Ok->value, $favorites) : $this->apiResponse(404, ReturnMessages::NotFound->value);
    }

    /**
     * Show the form for creating a new resource.
     * @param $product_id
     * @param Request $request
     * @return Response
     */
    public function create(Request $request,$product_id): Response
    {
        $favorite = Favorite::create([
           'product_id' => $product_id,
           'user_id' => $request->user()->id
        ]);

        return $favorite ? $this->apiResponse(200, ReturnMessages::Ok->value, $favorite) : $this->apiResponse(500, ReturnMessages::Error->value);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $favorite_id
     * @return Response
     */
    public function show(Request $request,$favorite_id): Response
    {
        $favorite = Favorite::find($favorite_id)?->firstWhere('user_id',$request->user()->id);

        return $favorite ? $this->apiResponse(200, ReturnMessages::Ok->value, $favorite) : $this->apiResponse(404, ReturnMessages::NotFound->value);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $favorite_id
     * @return Response
     */
    public function destroy(Request $request,$favorite_id): Response
    {
        $favorite = Favorite::find($favorite_id)->firstWhere('user_id',$request->user()->id);

        if($favorite) {
            $favorite->delete();
            return self::apiResponse(200, ReturnMessages::Ok->value);
        }

        return self::apiResponse(404,ReturnMessages::NotFound->value);
    }
}

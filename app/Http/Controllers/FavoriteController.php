<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application as Application2;
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

        if(count($favorites))
            return $this->apiResponse(200,'ok',$favorites);

        return $this->apiResponse(404,'Not found');
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

        if($favorite)
            return $this->apiResponse(200,'ok',$favorite);

        return $this->apiResponse(500,'Error');
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

        if($favorite)
            return $this->apiResponse(200,'ok',$favorite);

        return $this->apiResponse(404,'Not found');
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
            return $this->apiResponse(200, 'ok');
        }

        return $this->apiResponse(404,'Not found');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     * @param Request $request
     */
    public function index(Request $request)
    {
        $favorites = Favorite::latest()->where('user_id',$request->user()->id)->get();

        count($favorites) ? self::ok($favorites) : self::notFound();
    }

    /**
     * Show the form for creating a new resource.
     * @param $product_id
     * @param Request $request
     */
    public function create(Request $request,$product_id)
    {
        $favorite = Favorite::create([
           'product_id' => $product_id,
           'user_id' => $request->user()->id
        ]);

        $favorite ? self::ok($favorite) : self::unHandledError();
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $favorite_id
     */
    public function show(Request $request,$favorite_id)
    {
        $favorite = Favorite::find($favorite_id)?->firstWhere('user_id',$request->user()->id);

        $favorite ? self::ok($favorite) : self::notFound();
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $favorite_id
     */
    public function destroy(Request $request,$favorite_id)
    {
        $favorite = Favorite::find($favorite_id)->firstWhere('user_id',$request->user()->id);

        if($favorite) {
            $favorite->delete();
            self::ok();
        }

        self::notFound();
    }
}

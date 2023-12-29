<?php

namespace App\Http\Controllers;

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
        self::get_all_favorites_by_user($request);
    }

    /**
     * Show the form for creating a new resource.
     * @param $product_id
     * @param Request $request
     */
    public function create(Request $request,$product_id)
    {
        self::create_favorite($request->user()->id,$product_id);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $favorite_id
     */
    public function show(Request $request,$favorite_id)
    {
        self::get_user_favorite_by_id($favorite_id,$request->user()->id);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $favorite_id
     */
    public function destroy(Request $request,$favorite_id)
    {
        self::delete_user_favorite($favorite_id,$request->user()->id);
    }
}

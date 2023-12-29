<?php

namespace App\Http\Controllers;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        self::get_all_categories();
    }
}

<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CategorySellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @return void
     */
    public function index(Category $category)
    {
        $sellers = $category->products()
            ->with('seller')
            ->get()
            ->pluck('seller')
            ->unique();
//            ->values();

        return $this->showAll($sellers);
    }
}

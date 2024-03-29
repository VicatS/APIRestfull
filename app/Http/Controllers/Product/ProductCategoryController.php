<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only([
            'index'
        ]);

        $this->middleware('auth:api')->except([
            'index'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @param Category $category
     * @return JsonResponse|void
     */
    public function update(Request $request, Product $product, Category $category)
    {
        // sync, attach, syncWithoutDetaching
        $product->categories()->syncWithoutDetaching([ // para actualizar y mantener las categorias existentes
            $category->id
        ]);

        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse|void
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse(
                'La categoria especificada no es una categoria de este producto',
                404
            );
        }

        $product->categories()->detach([
           $category->id
        ]);

        return $this->showAll($product->categories);
    }
}

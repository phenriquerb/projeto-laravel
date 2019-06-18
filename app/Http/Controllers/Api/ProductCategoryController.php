<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Requests\ProductCategoryRequest;
use CodeShopping\Http\Resources\ProductCategoryResource;
use CodeShopping\Models\Category;
use CodeShopping\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use CodeShopping\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function index(Product $product)
    {
        //return $product->categories;
        return new ProductCategoryResource($product);
    }

    public function store(ProductCategoryRequest $request, Product $product)
    {
        //return $product->categories()->sync($request->categories);
        $changed = $product->categories()->sync($request->categories);
        $categoriesAttachedId = $changed['attached'];
        /** @var Collection $categories */
        $categories = Category::whereIn('id', $categoriesAttachedId)->get();
        //return $categories;
        //return $categories->count() ? response()->json($categories, 201) : $categories;
        return $categories->count() ? response()->json(new ProductCategoryResource($product), 201) : [];

    }

    public function destroy(Product $product, Category $category)
    {
        $product->categories()->detach($category->id);
        return response()->json([],204);
    }
}

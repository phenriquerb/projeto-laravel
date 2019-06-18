<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\CategoryRequest;
use CodeShopping\Http\Resources\CategoryResource;
use CodeShopping\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        //return Category::all();
        return CategoryResource::collection(Category::all());
    }

    public function store(CategoryRequest $request)
    {
        //$category = Category::create($request->all() + ['slug' => 'teste']);
        $category = Category::create($request->all());
        $category->refresh(); // atualiza pra trazer o campo active
        return $category;
    }

    public function show(Category $category)
    {
        //return $category;
        return new CategoryResource($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->fill($request->all());
        $category->save();
        return $category;
        //return response(['erro' => 0]); // <<funciona
        //return response([],204);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([],204);
    }
}

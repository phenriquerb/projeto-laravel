<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\ProductOutputRequest;
use CodeShopping\Http\Resources\ProductOutputResource;
use CodeShopping\Models\ProductOutput;

class ProductOutputController extends Controller
{
    public function index()
    {
        $inputs =  ProductOutput::with('product')->paginate(); //eager loading | lazy loading
        return ProductOutputResource::collection($inputs);
    }

    public function store(ProductOutputRequest $request)
    {
        $input = ProductOutput::create($request->all());
        return new ProductOutputResource($input);
    }

    public function show(ProductOutput $output)
    {
        return new ProductOutputResource($output);
    }
}

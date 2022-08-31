<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\ResourceProducts;
use App\Models\Product;
use App\Exceptions\HttpResponse\InternalServerError;
use App\Exceptions\HttpResponse\NotFound;
use App\Exceptions\ResourceNotFound;

class ProductController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('auth:api'); //Need authorised
    }

    public function index()
    {
        try {
            /** @var Products|null */
            $product = Product::all();
            return ResourceProducts::collection($product);
        } catch (ResourceNotFound $e) {
            throw new NotFound($e->getMessage());
        } catch (\Throwable $e) {
            throw new InternalServerError();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|integer',
            'description' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:1000'
        ]);

        $file = $request->file('thumbnail');
        $fileName = $file->hashName();
        $request->file('thumbnail')->storeAs('public/images/thumbnail/product', $fileName);

        $products = Product::create([
            'name'          => $request->name,
            'category'      => $request->category,
            'price'         => $request->price,
            'description'   => $request->description,
            'thumbnail'     => $fileName,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'products' => $products,
        ]);
    }

    public function show($id)
    {
        try {
            /** @var Product|null */
            // $product = Product::findByIdOrFail($id);
            // return ResourceProducts::make($product);
            return new ResourceProducts(Product::findByIdOrFail($id));
        } catch (ResourceNotFound $e) {
            throw new NotFound($e->getMessage());
        } catch (\Throwable $e) {
            throw new InternalServerError();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|integer',
            'description' => 'required|string',
            'thumbnail' => 'required|string|max:255',
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->thumbnail = $request->thumbnail;
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'products' => $product,
        ]);
    }

    public function destroy($id)
    {
        $products = Product::find($id);
        $products->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
            'products' => $products,
        ]);
    }
}

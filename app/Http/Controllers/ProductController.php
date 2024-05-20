<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private function getProduct(int $id): Product
    {
        try {
            return Product::findOrFail($id);
        } catch (Exception) {
            throw new HttpResponseException(response()->json([
                'errprs' => [
                    'message' => 'Product not found'
                ]
            ]));
        }
    }

    private function saveImage(UploadedFile $image): string
    {
        $path = "uploads/images/products/" . uniqid() . "." . $image->extension();
        $image->move(public_path('uploads/images/products'), $path);
        return $path;
    }

    private function removeImage(string $path): bool
    {
        if (file_exists(public_path($path))) {
            return unlink(public_path($path));
        }
        return false;
    }

    public function getAll(): JsonResponse
    {

        $products = Product::all();
        return response()->json([
            'data' => $products
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $dataRequest = [];

        try {
            $dataRequest = $request->validate([
                'name' => ['required', 'string', 'unique:products,name'],
                'price' => ['required', 'numeric', 'min:0'],
                'description' => [],
                'category_id' => ['required', 'exists:categories,id'],
                'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
                'expired_at' => ['date']
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'errors' => $validationException->errors()
            ], 400);
        }

        $image = $request->file('image');
        $dataRequest['image'] = $this->saveImage($image);
        $dataRequest['modified_by'] = Auth::user()->email;
        $product = Product::create($dataRequest);

        return response()->json([
            'data' => $product
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $dataRequest = [];

        try {
            $dataRequest = $request->validate([
                'name' => ['required', 'string'],
                'description' => [],
                'price' => ['required', 'numeric', 'min:0'],
                'category_id' => ['required', 'exists:categories,id'],
                'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
                'expired_at' => ['date']
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'errors' => $validationException->errors()
            ], 400);
        }

        $dataRequest['modified_by'] = Auth::user()->email;
        $product = Product::find($id);

        $this->removeImage($product->image);
        $dataRequest['image'] = $this->saveImage($request->file('image'));

        $product->update($dataRequest);

        return response()->json([
            'data' => $product
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $product = $this->getProduct($id);
        $this->removeImage($product->image);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}

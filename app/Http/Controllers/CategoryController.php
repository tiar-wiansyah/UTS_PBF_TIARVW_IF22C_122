<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    private function getCategory(int $id): Category
    {
        $category = Category::find($id);

        if (!$category) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Category not found'
                ]
            ], 400));
        }
        return $category;
    }

    public function getAll(): JsonResponse
    {
        $categories = Category::all();
        return response()->json([
            'data' => $categories
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $dataRequest = [];

        try {
            $dataRequest = $request->validate([
                'name' => ['required', 'unique:categories,name'],
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'errors' => $validationException->errors()
            ], 400);
        }

        $category = Category::create($dataRequest);
        return response()->json([
            'data' => $category
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $dataRequest = [];

        try {
            $dataRequest = $request->validate([
                'name' => ['required'],
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'errors' => $validationException->errors()
            ], 400);
        }

        $category = $this->getCategory($id);
        $category->update($dataRequest);

        return response()->json([
            'data' => $category
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $category = $this->getCategory($id);
        $category->delete();
        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}

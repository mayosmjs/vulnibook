<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // Only "admins" can create categories (via forged JWT 'role' claim)
    public function store(Request $request)
    {
        $payload = $request->get('auth_user');

        if (($payload['role'] ?? '') !== 'admin') {
            return response()->json(['error' => 'Unauthorized – Admins only'], 403);
        }

        $category = Category::create([
            'name' => $request->input('name') ?? 'Untitled'
        ]);

        return response()->json(['message' => 'Category created', 'category' => $category]);
    }

    
    public function update(Request $request, $id)
    {
        $payload = $request->get('auth_user');

        if (($payload['role'] ?? '') !== 'admin') {
            return response()->json(['error' => 'Unauthorized – Admins only'], 403);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->name = $request->input('name', $category->name);
        $category->save();

        return response()->json(['message' => 'Category updated', 'category' => $category]);
    }


    public function destroy(Request $request, $id)
    {
        $payload = $request->get('auth_user');

        if (($payload['role'] ?? '') !== 'admin') {
            return response()->json(['error' => 'Unauthorized – Admins only'], 403);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}

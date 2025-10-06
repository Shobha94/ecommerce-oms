<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return response()->json(Category::query()->withCount('products')->get());
    }

    public function store(Request $r) {
        $data = $r->validate(['name'=>'required|unique:categories','description'=>'nullable']);
        $cat = Category::create($data);
        return response()->json($cat, 201);
    }

    public function update(Request $r, int $id) {
        $cat = Category::findOrFail($id);
        $data = $r->validate(['name'=>"required|unique:categories,name,{$id}",'description'=>'nullable']);
        $cat->update($data);
        return response()->json($cat);
    }

    public function destroy(int $id) {
        Category::findOrFail($id)->delete();
        return response()->json(['message'=>'Deleted']);
    }
}

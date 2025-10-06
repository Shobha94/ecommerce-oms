<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $r) {
        $filters = [
            'category' => $r->integer('category'),
            'min' => $r->float('min', null),
            'max' => $r->float('max', null),
            'q'   => $r->string('q', null),
            'page'=> $r->integer('page', 1),
        ];
        $cacheKey = 'products:'.md5(json_encode($filters));
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($r) {
            $q = Product::query()->with('category')
                ->when($r->filled('category'), fn($qq)=>$qq->where('category_id', $r->category))
                ->filterByPrice($r->min, $r->max)
                ->searchByName($r->q)
                ->orderBy('id','desc');

            return $q->paginate(10);
        });
    }

    public function show(int $id) {
        return Product::with('category')->findOrFail($id);
    }

    public function store(Request $r) {
        $data = $r->validate([
            'name'=>'required','description'=>'nullable',
            'price'=>'required|numeric|min:0','stock'=>'required|integer|min:0',
            'category_id'=>'required|exists:categories,id'
        ]);
        $p = Product::create($data);
        Cache::flush(); // simple approach for demo
        return response()->json($p, 201);
    }

    public function update(Request $r, int $id) {
        $p = Product::findOrFail($id);
        $data = $r->validate([
            'name'=>'required','description'=>'nullable',
            'price'=>'required|numeric|min:0','stock'=>'required|integer|min:0',
            'category_id'=>'required|exists:categories,id'
        ]);
        $p->update($data);
        Cache::flush();
        return response()->json($p);
    }

    public function destroy(int $id) {
        Product::findOrFail($id)->delete();
        Cache::flush();
        return response()->json(['message'=>'Deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $r) {
        $items = Cart::with('product')->where('user_id', $r->user()->id)->get();
        return response()->json($items);
    }

    public function store(Request $r) {
        $data = $r->validate([
            'product_id'=>'required|exists:products,id',
            'quantity'=>'required|integer|min:1'
        ]);

        $item = Cart::updateOrCreate(
            ['user_id'=>$r->user()->id, 'product_id'=>$data['product_id']],
            ['quantity'=>\DB::raw('LEAST(quantity + '.(int)$data['quantity'].', 9999)')]
        );

        return response()->json($item->fresh('product'), 201);
    }

    public function update(Request $r, int $id) {
        $data = $r->validate(['quantity'=>'required|integer|min:1']);
        $item = Cart::where('user_id',$r->user()->id)->findOrFail($id);
        $item->update(['quantity'=>$data['quantity']]);
        return response()->json($item->fresh('product'));
    }

    public function destroy(Request $r, int $id) {
        Cart::where('user_id',$r->user()->id)->findOrFail($id)->delete();
        return response()->json(['message'=>'Removed']);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\CommonQueryScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use CommonQueryScopes, HasFactory;

    protected $fillable = ['name','description','price','stock','category_id'];

    public function category() { return $this->belongsTo(Category::class); }
    public function carts()    { return $this->hasMany(Cart::class); }
    public function orders()   { return $this->belongsToMany(Order::class)->withPivot(['quantity','price'])->withTimestamps(); }
}


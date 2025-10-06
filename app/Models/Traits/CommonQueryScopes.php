<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function scopeFilterByPrice(Builder $q, ?float $min, ?float $max): Builder {
        if ($min !== null) $q->where('price', '>=', $min);
        if ($max !== null) $q->where('price', '<=', $max);
        return $q;
    }

    public function scopeSearchByName(Builder $q, ?string $term): Builder {
        if ($term) $q->where('name', 'like', "%{$term}%");
        return $q;
    }
}

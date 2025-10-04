<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommonQueryScopes
{
    public function searchByTitle(Builder $query, string $search): Builder
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    public function filterByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }
}
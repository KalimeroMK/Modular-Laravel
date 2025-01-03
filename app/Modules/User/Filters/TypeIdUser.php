<?php

namespace App\Modules\User\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserTypeId
{
    /**
     * Apply the filter to the query.
     *
     * @param  Builder  $query
     * @param  mixed    $value
     * @return Builder
     */
    public function apply(Builder $query, mixed $value)
    {
        return $query->where('type_id', $value);
    }
}

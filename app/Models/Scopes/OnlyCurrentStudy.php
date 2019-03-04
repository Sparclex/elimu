<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OnlyCurrentStudy implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder $builder
     * @param  Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!Auth::check()) {
            abort(401);
        }
        $builder->where($model->qualifyColumn('study_id'), Auth::user()->study_id);
    }
}

<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OnlyCurrentStudy implements Scope
{
    /**
     * @var null
     */
    private $relation;

    public function __construct($relation = null)
    {
        $this->relation = $relation;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if($this->relation) {
            $builder->whereHas($this->relation);
        } else {
            $builder->where('study_id', Auth::user()->study_id);
        }

    }
}

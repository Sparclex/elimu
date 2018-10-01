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
    private $related;
    private $table;

    public function __construct($table, $related = false)
    {
        $this->related = $related;
        $this->table = $table;
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
        if ($this->related) {
            $builder->whereHas($this->table);
        } else {
            $builder->where($this->table.'.study_id', Auth::user()->study_id);
        }
    }
}

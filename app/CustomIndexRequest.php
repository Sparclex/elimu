<?php

namespace App;

use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class CustomIndexRequest extends ResourceIndexRequest
{
    public function buildCountQuery($query)
    {
        $baseQuery = $query->toBase();

        if (empty($baseQuery->groups)) {
            return $baseQuery;
        }

        $subQuery = $baseQuery->cloneWithout(
            $baseQuery->unions ? ['orders', 'limit', 'offset'] : ['orders', 'limit', 'offset']
        )->cloneWithoutBindings(
            $baseQuery->unions ? ['order'] : ['order']
        );


        return $query->getConnection()
            ->query()
            ->fromSub($subQuery, 'count_temp');
    }
}

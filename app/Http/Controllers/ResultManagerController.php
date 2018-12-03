<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Nova\Sample as SampleResource;
use App\Nova\Result as ResultResource;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResultManagerController
{
    public function handle()
    {
        $request = NovaRequest::capture();
        $results = Sample::simplePaginate(15);
        return response()->json([
            'label' => 'Results',
            'resources' => $results->getCollection()->mapInto(ResultResource::class)->map->serializeForIndex($request),
            'prev_page_url' => $results->previousPageUrl(),
            'next_page_url' => $results->nextPageUrl(),
            'softDeletes' => false,
        ]);
    }
}

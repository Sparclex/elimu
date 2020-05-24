<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('studies/{study}/{assay}/results', \App\Http\Controllers\Api\ListAllResultsController::class)
    ->middleware(\App\Http\Middleware\ForceUseJson::class, 'auth:api');




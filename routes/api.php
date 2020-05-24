<?php

use Laravel\Nova\Http\Middleware\Authenticate;


Route::get('studies/{study}/{assay}/results', \App\Http\Controllers\Api\ListAllResultsController::class)
    ->middleware(\App\Http\Middleware\ForceUseJson::class, 'auth:api');




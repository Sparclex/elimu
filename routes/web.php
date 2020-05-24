<?php

use Laravel\Nova\Http\Middleware\Authenticate;

Route::redirect('/', '/app');

Route::redirect('/nova', '/app');

Route::get('token', \App\Http\Controllers\Api\ShowTokenController::class)->middleWare(Authenticate::class)->name('showToken');

<?php

use Nathanmac\Utilities\Parser\Facades\Parser;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function() {
    return collect(Parser::xml(file_get_contents(storage_path('app/rdml.xml'))));
});

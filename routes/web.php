<?php

use Nathanmac\Utilities\Parser\Facades\Parser;

Route::redirect('/', '/nova');

Route::get('/test', function() {
    $data = \App\RdmlParser::make(storage_path('app/rdml.xml'));
    return view('test', compact('data'));
});

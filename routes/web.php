<?php

use Nathanmac\Utilities\Parser\Facades\Parser;

Route::redirect('/', '/nova');

Route::get('/test', function() {
    return collect(Parser::xml(file_get_contents(storage_path('app/rdml.xml'))));
});

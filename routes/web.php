<?php

use Nathanmac\Utilities\Parser\Facades\Parser;

Route::redirect('/', '/nova');

Route::get('/test', function() {
    $manager = new \App\RdmlManager(file_get_contents(storage_path('app/rdml.xml')), [
        'Pspp18S' => 100,
        'HsRNaseP' => 100,
        'PfvarATS' => 200,
    ]);
    $data = $manager->getChartData();

    return compact('data');
});

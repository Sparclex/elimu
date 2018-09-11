<?php

use App\ResultHandlers\Rdml\Processor;
use Nathanmac\Utilities\Parser\Facades\Parser;

Route::redirect('/', '/nova');

Route::get('/test', function() {
    $manager = new Processor(file_get_contents(base_path('tests/resources/one-sample.xml')), [
        'Pspp18S' => 100,
        'HsRNaseP' => 100,
        'PfvarATS' => 200,
    ]);
    $data = $manager->getControlSamples();

    return $data;
});

<?php

use App\ResultHandlers\Rdml\Processor;
use Nathanmac\Utilities\Parser\Facades\Parser;

Route::redirect('/', '/nova');

Route::get('/test', function() {
    $experiment = \App\Models\Experiment::find(1);
    return \PDF::loadView('reports.experiment', compact('experiment'))->setPaper('a4', 'landscape')->stream('download.pdf');
});

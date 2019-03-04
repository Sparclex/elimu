<?php

use App\Experiments\QPCR;

return [
    'result_types' => [
        'qPcr Rdml' => QPCR::class,
        'Excel' => \App\ResultHandlers\CsvResultHandler::class,
    ],
    'version' => '0.0.1',
    'name' => 'IHI Lims'
];

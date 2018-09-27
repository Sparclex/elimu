<?php

return [
    'result_types' => [
        'RDML' => \App\ResultHandlers\RdmlResultHandler::class,
        'CSV' => \App\ResultHandlers\CsvResultHandler::class,
    ],
    'version' => '0.0.1',
    'name' => 'Swiss TPH Lims'
];

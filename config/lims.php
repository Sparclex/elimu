<?php

return [
    'result_types' => [
        'qPcr Rdml' => \App\ResultHandlers\RdmlResultHandler::class,
        'Excel' => \App\ResultHandlers\CsvResultHandler::class,
    ],
    'version' => '0.0.1',
    'name' => 'IHI Lims'
];

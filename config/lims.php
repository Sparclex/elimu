<?php

use App\Experiments\QPCR;

return [
    'result_types' => [
        'qPcr Rdml' => QPCR::class,
        'Excel' => \App\ResultHandlers\CsvResultHandler::class,
    ],
    'version' => '1.0.0',
    'name' => 'Elimu'
];

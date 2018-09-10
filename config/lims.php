<?php

return [
    'result_types' => [
        'RDML' => \App\ResultHandlers\RdmlResultHandler::class,
        'CSV' => \App\ResultHandlers\CsvResultHandler::class,
    ]
];

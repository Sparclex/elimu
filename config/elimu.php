<?php

use App\Experiments\NonQPCR;
use App\Experiments\QPCR;

return [
    'result_types' => [
        'qPcr Rdml' => QPCR::class,
        'Excel' => NonQPCR::class,
    ],
    'version' => '1.0.0',
    'name' => 'Elimu'
];

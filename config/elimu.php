<?php

use App\Experiments\NonQPCR;
use App\Experiments\QPCR;
use App\Experiments\QPCRWithMelting;

return [
    'result_types' => [
        'qPCR RDML' => QPCR::class,
        'qPCR RDML with melting temperature' => QPCRWithMelting::class,
        'Non-qPCR' => NonQPCR::class,
    ],
    'version' => '1.0.0',
    'name' => 'Elimu'
];

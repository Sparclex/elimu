<?php

namespace App\ResultHandlers;

interface Extractor
{
    /**
     * @param $dataId integer representing the data / file in the database
     * @return array missing sample warnings
     */
    public function handle($dataId);
}

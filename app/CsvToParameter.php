<?php

namespace App;

use Illuminate\Http\Request;

class CsvToParameter
{
    public function __invoke(Request $request, $model)
    {
        return [
            'parameters' => CSVReader::make($request->parameters->getRealPath())->toArray(),
        ];
    }
}

<?php

namespace App;

use App\FileTypes\CSV;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CsvToParameter
{
    public function __invoke(Request $request, $model)
    {
        $parameters = CSV::make($request->parameter_file->getRealPath())->toArray();
        if ($model->parameter_file) {
            $parameterFile = $request->parameter_file->storeAs('', $model->parameter_file);
        } else {
            $parameterFile = $request->parameter_file
                ->storeAs('input_parameters', Str::random(20).".".
                    $request->parameter_file->getClientOriginalExtension());
        }
        return [
            'parameters' => $parameters,
            'parameter_file' => $parameterFile
        ];
    }
}

<?php

namespace App\Rules;

use App\Models\StorageSize;
use Illuminate\Support\Facades\Auth;

class StorageSizeExists
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        if ($value && $value > 0) {
            return StorageSize::where([
                'study_id' => Auth::user()->study_id,
                'sample_type_id' => $validator->getData()[$parameters[1]]
            ])->exists();
        }
        return true;
    }
}

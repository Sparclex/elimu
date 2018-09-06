<?php

namespace Sparclex\Lims\Rules;

use Sparclex\Lims\Models\StorageSize;

class StorageSizeExists
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        if($value && $value > 0) {
            return StorageSize::where([
                'study_id' => $validator->getData()[$parameters[0]],
                'sample_type_id' => $validator->getData()[$parameters[1]]
            ])->exists();
        }
        return true;
    }
}

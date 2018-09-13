<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class DataFile implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(! $value instanceof UploadedFile) {
            return false;
        }
        switch($value->getClientOriginalExtension()) {
            case 'rdml':
                return $this->isValidRdml($value);
            case 'csv':
                return $this->isValidCsv($value);
            default:
                return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given file is not a parsable result file.';
    }

    protected function isValidRdml($value)
    {
        return true;
    }

    protected function isValidCsv($value)
    {
        return true;
    }
}

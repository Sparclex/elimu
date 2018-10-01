<?php

namespace App\Utility;

use App\Rules\DataFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileSaver
{
    public static function save(UploadedFile $file, $experimentId)
    {
        (new DataFile($experimentId))->validate('file', $file);
        switch ($file->getClientOriginalExtension()) {
            case 'rdml':
                return self::saveRdml($file);
                break;
            case 'csv':
                return self::saveCsv($file);
                break;
        }
        throw ValidationException::withMessages([
            'file' => __('Unsupported file type')
        ]);
    }

    public static function saveRdml(UploadedFile $file)
    {
        $zip = new \ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \Exception('Cannot unpack the rdml file');
        }
        $storagePath = 'experiment-data/' . Str::random(40);
        $zip->extractTo(storage_path('app/' . $storagePath));
        $zip->close();
        $file = Storage::files($storagePath)[0];
        return $file;
    }

    public static function saveCsv(UploadedFile $file)
    {
        return $file->store('experiement_data');
    }
}

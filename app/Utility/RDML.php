<?php

namespace App\Utility;

use Illuminate\Http\UploadedFile;

class RDML
{
    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param $id
     * @return string
     * @throws \Exception
     */
    public static function toXml(UploadedFile $file, $id)
    {
        $zip = new \ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \Exception('Cannot unpack the rdml file');
        }
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $zip->extractTo(storage_path('app/experiment-data/'.$id), [$filename.".xml"]);
        $zip->close();

        return 'experiment-data/'.$id.'/'.$filename.".xml";
    }
}

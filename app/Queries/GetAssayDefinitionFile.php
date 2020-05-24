<?php

namespace App\Queries;

use App\Models\AssayDefinitionFile;
use Illuminate\Database\Eloquent\Builder;

class GetAssayDefinitionFile
{
    /**
     * @return AssayDefinitionFile
     */
    public function run(int $studyId, string $assayName)
    {
        return AssayDefinitionFile::withoutGlobalScopes()
            ->whereHas('assay', static function (Builder $builder) use ($studyId, $assayName) {
                return $builder
                    ->withoutGlobalScopes()
                    ->where('study_id', $studyId)
                    ->where('name', $assayName);
            })
            ->with([
                'assay' => function ($query) {
                    return $query->withoutGlobalScopes();
                },
            ])
            ->first();
    }
}

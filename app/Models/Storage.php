<?php

namespace App\Models;

use App\Models\Traits\SetUserStudyOnSave;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use SetUserStudyOnSave;

    protected $table = 'storage';

    protected $fillable = ['study_id', 'sample_type_id', 'sample_id', 'box', 'position'];

    public static function generateStoragePosition($sampleId, $studyId, $sampleTypeId, $quantity, $create = true)
    {
        if (!StorageSize::where('study_id', $studyId)
            ->where('sample_type_id', $sampleTypeId)
            ->exists()) {
            return false;
        }

        $latestPosition = self::latestPosition($studyId, $sampleTypeId);

        $newPositions = new Collection();
        for ($i = 0; $i < $quantity; $i++) {
            $latestPosition = $newPositions[] = $latestPosition->nextEntry($sampleId);
        }
        if ($quantity === 1) {
            $position = $newPositions->first();
            return $create ? $position->save() : $position;
        } else {
            return $create ? $newPositions->each(function ($position) {
                $position->save();
            }) : $newPositions;
        }
    }

    public static function latestPosition($studyId, $sampleTypeId)
    {
        return self::where('study_id', $studyId)
                ->where('sample_type_id', $sampleTypeId)
                ->orderByDesc('position')
                ->first()
            ?? new Storage([
                'position' => -1,
                'study_id' => $studyId,
                'sample_type_id' => $sampleTypeId
            ]);
    }

    public function nextEntry($sampleId)
    {
        return new static([
            'study_id' => $this->study_id,
            'sample_type_id' => $this->sample_type_id,
            'position' => $this->position + 1,
            'sample_id' => $sampleId
        ]);
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }
}

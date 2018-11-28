<?php

namespace App\Models;

use App\Scopes\OnlyCurrentStudy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Storage extends DependsOnStudy
{
    protected $table = 'storage';

    protected $fillable = ['study_id', 'sample_type_id', 'sample_id', 'box', 'position'];

    public static function generateStoragePosition($sampleId, $studyId, $sampleTypeId, $quantity, $create = true)
    {
        $size = StorageSize::sizeFor($studyId, $sampleTypeId);

        if (!$size) {
            return false;
        }
        $storage = self::latestPosition($studyId, $sampleTypeId);

        $newPositions = new Collection();
        for ($i = 0; $i < $quantity; $i++) {
            $storage = $newPositions[] = $storage->nextPosition($size, $sampleId);
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
        return self::where('study_id', $studyId)->where('sample_type_id', $sampleTypeId)->orderByDesc('id')->first()
            ?? new Storage(['box' => 1, 'position' => 0, 'sample_type_id' => $sampleTypeId, 'study_id' => $studyId]);
    }

    public function nextPosition($size, $sampleId)
    {
        $storage = new Storage([
            'box' => 1,
            'position' => 1,
            'sample_type_id' => $this->sample_type_id,
            'study_id' => $this->study_id,
            'sample_id' => $sampleId
        ]);

        if ($this->position + 1 > $size) {
            $storage->box = $this->box + 1;
        } else {
            $storage->box = $this->box;
            $storage->position = $this->position + 1;
        }
        return $storage;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OnlyCurrentStudy('storage'));
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class);
    }

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function exceedsBoxSize($size)
    {
        return $this->position + 1 > $size;
    }
}

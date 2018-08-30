<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    public $timestamps = false;

    protected $fillable = ['study_id', 'sample_type_id', 'box', 'field'];

    public function sample() {
        return $this->belongsToMany(Sample::class, 'storage_places');
    }

    public function getSampleAttribute() {
        return $this->relations['sample']->first();
    }

    public function sampleType() {
        return $this->belongsToMany(SampleType::class, 'storage_places');
    }

    public function study() {
        return $this->belongsTo(Study::class);
    }

    public function getSampleTypeAttribute() {
        return $this->relations['sampleType']->first();
    }


    public static function generateStorePlace($study_id, $sample_type_id, $create = true)
    {
        $box = 1;
        $field = 1;

        $size = StorageSize::sizeFor($study_id, $sample_type_id);
        if (! $size) {
            return false;
        }
        $storage = self::where('study_id', $study_id)
            ->where('study_id', $study_id)
            ->where('sample_type_id', $sample_type_id)
            ->orderByDesc('id')
            ->first();
        if($storage) {
            if($storage->field + 1 > $size) {
                $box = $storage->box + 1;
                $field = 1;
            }
            else {
                $box = $storage->box;
                $field = $storage->field + 1;
            }
        }
        if($create) {
            return Storage::create([
                'study_id' => $study_id,
                'sample_type_id' => $sample_type_id,
                'box' => $box,
                'field' => $field,
            ]);
        }
        return [
            'box' => $box,
            'place' => $field,
            'size' => $size
        ];
    }
}

<?php

namespace App\Observers;

use App\Models\SampleMutation;
use App\Models\Storage;
use App\Support\StoragePointer;

class StorageGenerator
{

    public function created(SampleMutation $sampleMutation)
    {

        $pointer = new StoragePointer($sampleMutation->sample_type_id);

        $pointer->store($sampleMutation->sample_id, $sampleMutation->quantity);
    }

    public function updating(SampleMutation $sampleMutation)
    {

        if ($sampleMutation->quantity < $sampleMutation->getOriginal('quantity')) {
            Storage::where('sample_id', $sampleMutation->sample_id)
                ->where('sample_type_id', $sampleMutation->sample_type_id)
                ->where('study_id', auth()->user()->study_id)
                ->orderByDesc('position')
                ->limit($sampleMutation->getOriginal('quantity') - $sampleMutation->quantity)
                ->delete();
        } elseif ($sampleMutation->quantity > $sampleMutation->getOriginal('quantity')) {
            $pointer = new StoragePointer($sampleMutation->sample_type_id, $sampleMutation->study_id);

            $pointer->store(
                $sampleMutation->sample_id,
                $sampleMutation->quantity - $sampleMutation->getOriginal('quantity')
            );
        }
    }
}

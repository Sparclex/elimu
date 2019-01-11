<?php

namespace App\Observers;

use App\Models\SampleMutation;
use App\Models\Storage;

class AutoStorageSaver
{
    /**
     * Generates additional storage positions if the quantity has been increased
     * Deletes the most recent storage positions if the quantity has been reduced
     *
     * @param  \App\Models\SampleMutation $sample
     * @return void
     */
    public function updated(SampleMutation $sample)
    {
        if ($sample->getOriginal('quantity') > $sample->quantity) {
            Storage::where([
                'sample_id' => $sample->id,
                'study_id' => $sample->study_id,
                'sample_type_id' => $sample->sample_type_id
            ])->orderByDesc('id')
                ->limit($sample->getOriginal('quantity') - $sample->quantity)
                ->delete();
        } elseif ($sample->getOriginal('quantity') < $sample->quantity) {
            Storage::generateStoragePosition(
                $sample->id,
                $sample->study_id,
                $sample->sample_type_id,
                $sample->quantity - $sample->getOriginal('quantity')
            );
        }
    }

    /**
     * Generates storage position according to the quantity
     *
     * @param  \App\Models\SampleMutation $sample
     * @return void
     */
    public function created(SampleMutation $sample)
    {
        if ($sample->quantity == 0) {
            return;
        }
        Storage::generateStoragePosition(
            $sample->id,
            $sample->study_id,
            $sample->sample_type_id,
            $sample->quantity
        );
    }
}

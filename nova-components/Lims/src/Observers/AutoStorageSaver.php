<?php

namespace Sparclex\Lims\Observers;

use Sparclex\Lims\Models\Sample;
use Sparclex\Lims\Models\Storage;

class AutoStorageSaver
{
    /**
     * Generates additional storage positions if the quantity has been increased
     * Deletes the most recent storage positions if the quantity has been reduced
     *
     * @param  \Sparclex\Lims\Models\Sample $sample
     * @return void
     */
    public function updated(Sample $sample)
    {
        dd('testy');
        if ($sample->getOriginal('quantity') > $sample->quantity) {
            Storage::where([
                'sample_id' => $sample->id,
                'study_id' => $sample->study_id,
                'sample_type_id' => $sample->sample_type_id
            ])->orderByDesc('id')
                ->limit($sample->getOriginal('quantity') - $sample->quantity)
                ->delete();
        } else if ($sample->getOriginal('quantity') < $sample->quantity) {
            Storage::generateStoragePosition(
                $sample->id,
                $sample->study_id,
                $sample->sample_type_id,
                $sample->quantity - $sample->getOriginal('quantity'));
        }
    }

    /**
     * Generates storage position according to the quantity
     *
     * @param  \Sparclex\Lims\Models\Sample $sample
     * @return void
     */
    public function created(Sample $sample)
    {
        dd('test');
        Storage::generateStoragePosition($sample->id, $sample->study_id, $sample->sample_type_id, $sample->quantity);
    }
}

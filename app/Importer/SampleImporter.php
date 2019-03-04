<?php

namespace App\Importer;

use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Storage;
use App\Support\StoragePointer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SampleImporter implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    protected $attributes;

    protected $rules;

    protected $modelClass;

    protected $sampleTypeIdsWithStorage;

    public function __construct($resource, $attributes, $rules, $modelClass)
    {
        $this->resource = $resource;
        $this->attributes = $attributes;
        $this->rules = $rules;
        $this->modelClass = $modelClass;
        $this->sampleTypeIdsWithStorage = Auth::user()->study->sampleTypes->pluck('id');
    }

    public function collection(Collection $rows)
    {
        $sampleTypes = [];
        $sampleTypeStorage = [];

        Validator::make($rows->toArray(), $this->rules())->validate();

        $rows = $rows->first()->has('position') ? $rows->sortBy('position') : $rows;

        foreach ($rows->pluck('type')->unique() as $name) {
            $sampleTypes[$name] = SampleType::firstOrCreate(compact('name'));
            $sampleTypeStorage[$name] = new StoragePointer($sampleTypes[$name]->id, auth()->user()->study_id);
        }

        $existingSamples = Sample::whereIn('sample_id', $rows->pluck('id'))
            ->pluck('id', 'sample_id');

        $newSamples = $rows->reject(function ($sample) use ($existingSamples) {
            return $existingSamples->keys()->contains($sample['id']);
        });

        $existingSampleMutation = DB::table('sample_mutations')
            ->whereIn('sample_id', $existingSamples->values())
            ->get();

        $newSampleMutation = $rows->diff($newSamples)->reject(function ($sample) use (
            $existingSampleMutation,
            $existingSamples,
            $sampleTypes
        ) {
            foreach ($existingSampleMutation as $sampleMutation) {
                if (isset($existingSamples[$sampleMutation['sample_id']])
                    && $sample['id'] == $existingSamples[$sampleMutation['sample_id']]
                    && $sampleTypes[$sample['type']]->id == $sampleMutation['sample_type_id']) {
                    return true;
                }
            }
            return false;
        });

        $newPositions = [];

        foreach ($newSamples as $sample) {
            $sampleInformation = $this->saveSampleInformation($sample);

            $this->saveMutation($sample, $sampleInformation->id, $sampleTypes[$sample['type']]);

            $newPositions = $sampleTypeStorage[$sample['type']]
                ->store($sampleInformation->id, $sample['quantity'], false);
        }

        foreach ($newSampleMutation as $mutation) {
            $this->saveMutation($mutation, $existingSamples[$mutation['id']], $sampleTypes[$mutation['type']]);

            $newPositions = $sampleTypeStorage[$mutation['type']]
                ->store($existingSamples[$mutation['id']], $mutation['quantity'], false);
        }

        collect($newPositions)->chunk(200)->each(function ($positions) {
            Storage::insert($positions->toArray());
        });
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required',
            '*.type' => 'required',
            '*.collected_at' => 'nullable',
            '*.birthdate' => 'nullable',
            '*.gender' => 'nullable|size:1',
            '*.position' => 'nullable|numeric',
        ];
    }

    private function sampleInformationColumns()
    {
        return ['subject_id', 'visit_id', 'collected_at', 'birthdate', 'gender'];
    }

    private function saveSampleInformation(Collection $row)
    {
        $sampleInformation = new Sample;
        $sampleInformation->sample_id = $row['id'];

        foreach ($row->only($this->sampleInformationColumns()) as $key => $value) {
            if ($key == 'gender') {
                switch (strtolower($value)) {
                    case 'm':
                        $value = 0;
                        break;
                    case 'f':
                        $value = 1;
                        break;
                    default:
                        $value = null;
                }
            }

            if ($key == 'collected_at') {
                $value = Date::excelToDateTimeObject($value);
            }

            $sampleInformation->{$key} = $value;
        }

        $sampleInformation->save();

        return $sampleInformation;
    }

    private function saveMutation(Collection $row, $sampleId, $sampleType)
    {
        DB::table('sample_mutations')->insert([
            'sample_id' => $sampleId,
            'sample_type_id' => $sampleType->id,
            'quantity' => $row['quantity'] ?? 0,
        ]);
    }
}

<?php
namespace App\Importer;

use App\Models\Sample;
use App\Models\SampleType;
use App\Models\SampleInformation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Sparclex\NovaImportCard\ImportException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SampleInformationImporter implements ToCollection, WithHeadingRow, WithValidation
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
        $sampleTypes[] = [];

        Validator::make($rows->toArray(), $this->rules())->validate();

        $rows = $rows->first()->has('position') ? $rows->sortBy('position') : $rows;
        foreach ($rows->pluck('type')->unique() as $name) {
            $sampleTypes[$name] = SampleType::firstOrCreate(compact('name'));
        }

        foreach ($rows as $row) {
            $sampleInformation = $this->saveSampleInformation($row);

            $this->saveSample($row, $sampleInformation, $sampleTypes[$row['type']]);
        }
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required',
            '*.type' => 'required',
            '*.collected_at'=> 'nullable',
            '*.birthdate' => 'nullable|date',
            '*.gender' => 'nullable|size:1',
            '*.position' => 'numeric',
        ];
    }

    private function sampleInformationColumns()
    {
        return ['subject_id', 'visit_id', 'collected_at', 'birthdate', 'gender'];
    }

    private function saveSampleInformation(Collection $row)
    {

        $sampleInformation = SampleInformation::where('sample_id', $row['id'])->first();

        if (!$sampleInformation) {
            $sampleInformation = new SampleInformation;
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
        }

        return $sampleInformation;
    }

    private function saveSample(Collection $row, $sampleInformation, $sampleType)
    {
        $sample = Sample::firstOrNew([
            'sample_information_id' => $sampleInformation->id,
            'sample_type_id' => $sampleType->id
        ]);

        if ($sample->isDirty()) {
            if ($row->has('quantity')
                && !$this->storageSizeExists($sampleType->id)) {
                throw new ImportException(
                    sprintf('Not storage size defined for sample type \'%s\'', $sampleType->name)
                );
            }
            $sample->quantity = $row->get('quantity', null) ?: 0;

            $extra = [];

            foreach (array_except($row, $this->sampleInformationColumns()) as $key => $value) {
                if (starts_with($key, 'extra_')) {
                    $extra[str_replace_first('extra_', '', $key)] = $value;
                }
            }

            $sample->extra = $extra;
            $sample->save();
        }

        return $sample;
    }

    private function storageSizeExists($sampleTypeId)
    {
        return $this->sampleTypeIdsWithStorage->search($sampleTypeId) !== false;
    }
}

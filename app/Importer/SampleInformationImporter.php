<?php
namespace App\Importer;

use App\Models\Sample;
use App\Models\SampleType;
use App\Models\SampleInformation;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Sparclex\NovaImportCard\BasicImporter;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SampleInformationImporter extends BasicImporter
{

    protected $attributes;

    protected $rules;

    protected $modelClass;

    public function __construct($resource, $attributes, $rules, $modelClass)
    {
        $this->resource = $resource;
        $this->attributes = $attributes;
        $this->rules = $rules;
        $this->modelClass = $modelClass;
    }

    public function model(array $row)
    {
        $sampleInformation = $this->saveSampleInformation($row);

        $sampleType = SampleType::firstOrCreate(['name' => $row['type']]);

        $this->saveSample($row, $sampleInformation, $sampleType);

        return $sampleInformation;
    }

    public function rules(): array
    {
        return [
            'id' => 'required',
            'type' => 'required',
            'collected_at'=> 'nullable',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|size:1',
        ];
    }

    private function sampleInformationColumns()
    {
        return ['subject_id', 'visit_id', 'collected_at', 'birthdate', 'gender'];
    }

    private function saveSampleInformation($row)
    {
        $sampleInformation = SampleInformation::where('sample_id', $row['id'])->first();

        if (!$sampleInformation) {
            $sampleInformation = new SampleInformation;
            $sampleInformation->sample_id = $row['id'];

            foreach (array_only($row, $this->sampleInformationColumns()) as $key => $value) {
                if ($key == 'gender') {
                    $value = $value == 'M' ? 0 : 1;
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

    private function saveSample($row, $sampleInformation, $sampleType)
    {
        $sample = Sample::firstOrNew([
            'sample_information_id' => $sampleInformation->id,
            'sample_type_id' => $sampleType->id
        ]);

        if ($sample->isDirty()) {
            $sample->quantity = $row['quantity'] ?? 0;

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
}

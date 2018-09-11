<?php

use Illuminate\Database\Seeder;

class StudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studies = factory(\App\Models\Study::class, 5)->create();
        $this->call(SampleTypeSeeder::class);
        foreacH ($studies as $study) {

            $study->sampleTypes()->attach(
                1, [
                     'size' => 5,
                 ]);
            $study->sampleTypes()->attach(
                2, [
                'size' => 5,
            ]);
            $study->sampleTypes()->attach(
                3, [
                'size' => 5,
            ]);
        }
    }
}

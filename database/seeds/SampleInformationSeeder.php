<?php

use Illuminate\Database\Seeder;

class SampleInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\SampleInformation::class, 10)->create();
    }
}

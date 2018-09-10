<?php

use Illuminate\Database\Seeder;

class SampleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\SampleType::class, 10)->create();
    }
}

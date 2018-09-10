<?php

use Illuminate\Database\Seeder;

class ProcessingLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\ProcessingLog::class, 5)->create();
    }
}

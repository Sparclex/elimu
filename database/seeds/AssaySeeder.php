<?php

use Illuminate\Database\Seeder;

class AssaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Assay::class, 5)->create();
    }
}

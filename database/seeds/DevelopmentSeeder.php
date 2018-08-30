<?php

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Test::class, 10)->create();
        factory(App\User::class)->create([
            'email' => 'silvan.wehner@gmail.com',
            'password' => \Hash::make('12345'),
            'name' => 'Silvan Wehner'
        ]);
        foreach(['Whole blood',
                    'Serum',
                    'Stool',
                    'Urine',
                    'PBMC',
                    'Plasma',
                    'Pax',
                    'Pregnancy',
                    'GPCR',
                    'Microbiology'] as $type) {
            App\SampleType::create(['name' => $type]);
        }
        factory(App\ProcessingLog::class, 10)->create();


        \App\Study::find(1)->storageSizes()->createMany([
            ['size' => 23, 'sample_type_id' => 1],
            ['size' => 10, 'sample_type_id' => 2],
            ['size' => 2, 'sample_type_id' => 3],
        ]);
    }
}

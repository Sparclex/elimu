<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'email' => 'schindler.tobi@gmail.com',
            'name' => 'Tobias Schindler',
            'password' => \Illuminate\Support\Facades\Hash::make('ihibagamoyo'),
            'timezone' => 'Europe/Zurich',
            'role' => 'Administrator'
        ]);
        \App\User::create([
            'email' => 'silvan.wehner@gmail.com',
            'name' => 'Silvan Wehner',
            'password' => \Illuminate\Support\Facades\Hash::make('12345'),
            'timezone' => 'Africa/Dar_es_Salaam',
            'role' => 'Administrator'
        ]);
    }
}

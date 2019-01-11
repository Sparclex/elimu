<?php

use App\Models\Study;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(\App\User::class)->create([
            'name' => 'Silvan',
            'email' => 'silvan.wehner@gmail.com',
            'password' => \Hash::make('12345')
        ]);

        $study = factory(Study::class)->create([
            'study_id' => '12345',
            'name' => 'Test Study'
        ]);

        $user->studies()->attach($study, [
            'power' => \App\Policies\Authorization::LABMANAGER,
            'selected' => true,
        ]);
    }
}

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
        factory(App\User::class)->create([
            'email' => 'silvan.wehner@gmail.com',
            'password' => \Hash::make('12345'),
            'name' => 'Silvan Wehner'
        ]);
        $this->call(AssaySeeder::class);
        $this->call(SampleSeeder::class);
    }
}

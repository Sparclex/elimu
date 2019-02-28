<?php

use App\Experiments\QPCR;
use App\Models\Assay;
use App\Models\Sample;
use App\Models\Study;
use Facades\Tests\Setup\ResultFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

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

        Auth::loginUsingId(1);

        ResultFactory::withParameters(collect([
            [
                'target' => 'PFvarts',
                'cutoff' => 42,
                'minvalues' => 2,
                'cuttoffstdev' => 5
            ]
        ]))
            ->withPositives(40)
            ->withNegatives(8)
            ->withError(QPCR::ERROR_REPLICAS)
            ->withError(QPCR::ERROR_STDDEV)
            ->withError(QPCR::ERROR_REPEAT)
            ->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProfileWorker;
use App\Models\Campaign;

class CampaignProfileWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Attach Profile

        $profiles = ProfileWorker::get();
        $campaignns = Campaign::get();
        $campaign = Campaign::orderby('created_at', 'desc')->first();


        foreach ($profiles as $profile) {
            foreach ($campaignns as $campaign) {
                // $user->roles()->sync([1 => ['expires' => true], 2, 3]);

                $campaign->profiles()->attach(
                    $profile->id,
                    [
                        'cost' => 0.0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                echo $campaign;
                echo $profile;
                echo PHP_EOL;
                echo PHP_EOL;
            }
        }
    }
}

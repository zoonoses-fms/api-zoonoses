<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plataform;
use App\Models\Core;
use App\Models\Ability;
use Database\Seeders\Location\The\SaadSeeder;
use Database\Seeders\Location\The\NeighborhoodSeeder;
use Database\Seeders\Location\The\NeighborhoodPopulationSeeder;
use Database\Seeders\Location\The\BlockSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $userSeeder = new UserSeeder();
        $userSeeder->run();

        $clientPlatformSeeder = new PlataformSeeder();
        $clientPlatformSeeder->run();

        $coreSeeder = new CoreSeeder();
        $coreSeeder->run();

        $abilitiesSeeder = new AbilitySeeder();
        $abilitiesSeeder->run();

        $user = User::find(1);
        $clientPlatform = Plataform::find(1);
        $core = Core::where('initial', 'Admin')->first();

        $user->plataforms()->attach($clientPlatform->id, ['created_at' => now(), 'updated_at' => now()]);
        $user->cores()->attach($core->id, ['created_at' => now(), 'updated_at' => now()]);

        foreach ($core->abilities as $ability) {
            $user->abilities()->attach($ability->id, ['created_at' => now(), 'updated_at' => now()]);
        }


        /**
         * NeighborhoodZonesSeeder
         * NeighborhoodSeeder
         * exlusivo para Zoonoses
         */

        $saadSeeder = new SaadSeeder();
        $neighborhoodSeeder = new NeighborhoodSeeder();
        $neighborhoodPopulationSeeder = new NeighborhoodPopulationSeeder();
        $blockSeeder = new BlockSeeder();

        $saadSeeder->run();
        $neighborhoodSeeder->run();
        $neighborhoodPopulationSeeder->run();
        $blockSeeder->run();
    }
}

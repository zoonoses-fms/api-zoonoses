<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ability;
use App\Models\Core;

class AbilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cores = Core::get();

        $abilites = [
            'name' => 'list', 'create', 'update', 'delete', 'show'];

        foreach ($cores as $core) {
            foreach ($abilites as $ability) {
                Ability::create(
                    [
                        'name' => "{$core->initial}:{$ability}",
                        'description' => "{$core->name}:{$ability}",
                        'core_id' => $core->id
                    ]
                );
            }
        }

        $admin = Core::where('initial', 'Admin')->first();
        Ability::create(
            [
                'name' => 'zoonoses:admin',
                'description' => 'Permissão Total',
                'core_id' => $admin->id
            ]
        );
    }
}

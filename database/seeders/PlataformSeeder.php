<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plataform;
use Illuminate\Support\Str;

class PlataformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plataform::create(
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'web',
                'description' => 'Plataforma WEB',
                'password' => hash('sha256', env('APP_KEY'))
            ]
        );

        Plataform::create(
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'movel',
                'description' => 'Plataforma Celular',
                'password' => hash('sha256', env('APP_KEY'))
            ]
        );

        $plataforms = Plataform::get();

        foreach ($plataforms as $plataform) {
            printf("\n {$plataform->name} : {$plataform->description} \n {$plataform->uuid} -> {$plataform->password} \n");
        }
    }
}

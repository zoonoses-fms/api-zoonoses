<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Core;

class CoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Core::create(
            [
                'name' => 'Zoonoses',
                'initial' => 'Admin'
            ]
        );

        Core::create(
            [
                'name' => 'Secretaria da Gerência',
                'initial' => 'Secretaria'
            ]
        );

        Core::create(
            [
                'name' => 'Núcleo de Controle de Roedores e Vetores',
                'initial' => 'NCRV'
            ]
        );

        Core::create(
            [
                'name' => 'Núcleo de Controle da Raiva, Leishmaniose e Outras Zoonoses',
                'initial' => 'NCRLO'
            ]
        );

        Core::create(
            [
                'name' => 'Núcleo de Correição',
                'initial' => 'NC'
            ]
        );
    }
}

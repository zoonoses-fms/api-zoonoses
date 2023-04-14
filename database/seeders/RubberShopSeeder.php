<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location\The\TheSaad;
use App\Models\Location\The\TheNeighborhoodAlias;
use App\Models\RubberShop;

class RubberShopSeeder extends Seeder
{
    public function nameCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI"))
    {
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
        }//foreach
        return $string;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = __DIR__ . '/files/the/rubber_shop.geojson';
        $file = file_get_contents($path, true);

        if ($file) {
            $fileRubberShop = json_decode($file, true);

            $features = $fileRubberShop['features'];

            foreach ($features as $feature) {
                $geometry = json_encode($feature['geometry']);

                $zone = TheSaad::where(
                    'standardized',
                    $this->nameCase($feature['properties']['saad'])
                )->first();

                $theNeighborhoodAlias = TheNeighborhoodAlias::getOrCreate($feature['properties']['localidade']);

                $rubberShop = new RubberShop();
                $rubberShop->code = $feature['properties']['Name'];
                $rubberShop->name = $feature['properties']['identifica'];
                $rubberShop->owner = $feature['properties']['proprietar'];
                $rubberShop->phone = $feature['properties']['telefone'];
                $rubberShop->supervisor = $feature['properties']['supervisor'];
                $rubberShop->address = $feature['properties']['endereco'];
                $rubberShop->number = $feature['properties']['num_imovel'];
                $rubberShop->cpf_cnpj = $feature['properties']['cpf_cnpj'];

                $rubberShop->saad_id = $zone->id;
                $rubberShop->the_neighborhood_alias_id = $theNeighborhoodAlias->id;

                $rubberShop->geometry = DB::raw("ST_GeomFromGeoJSON('{$geometry}')");

                $rubberShop->save();

            }

        }
    }
}

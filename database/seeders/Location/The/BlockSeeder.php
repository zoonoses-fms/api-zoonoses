<?php

namespace Database\Seeders\Location\The;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location\The\TheNeighborhood;

use App\Models\Location\The\TheBlock;
use App\Models\Location\The\TheBlockGeography;

class BlockSeeder extends Seeder
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
        $path = __DIR__ . '/../../files/the/the_blocks.geojson';
        $file = file_get_contents($path, true);

        if ($file) {
            $fileNeighborhood = json_decode($file, true);

            $features = $fileNeighborhood['features'];

            foreach ($features as $feature) {
                $geometry = json_encode($feature['geometry']);
                $neighborhood = TheNeighborhood::where(
                    'gid',
                    $feature['properties']['cod_bairro']
                )->first();

                if ($neighborhood == null) {
                    dd($feature);
                }
                $block = new TheBlock();

                $block->gid = $feature['properties']['gid'];
                $block->the_neighborhood_id = $neighborhood->id;

                $blockGeography = new TheBlockGeography();
                $blockGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
               //  DB::raw("ST_TRANSFORM(ST_GeomFromGeoJSON('{$geometry}'), 4326)");
                $block->save();
                $block->geography()->save($blockGeography);
            }
        }
    }
}

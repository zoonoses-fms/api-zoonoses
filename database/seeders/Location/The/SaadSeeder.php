<?php

namespace Database\Seeders\Location\The;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location\City;
use App\Models\Location\The\TheSaad;
use App\Models\Location\The\TheSaadGeography;

class SaadSeeder extends Seeder
{
    public function nameCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI"))
    {
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newWords = array();
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
                array_push($newWords, $word);
            }
            $string = join($delimiter, $newWords);
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
        $path = __DIR__ . '/../../files/the/the_saads.geojson';
        $file = file_get_contents($path, true);

        if ($file) {
            $filesaad = json_decode($file, true);

            $features = $filesaad['features'];

            foreach ($features as $feature) {
                $geometry = json_encode($feature['geometry']);
                $saad = new TheSaad();
                $name = trim(str_replace('SAAD ', '', $feature['properties']['nome']));
                $saad->name = $name;
                $saad->standardized = $this->nameCase($name );
                $saad->metaphone = $saad->getPhraseMetaphone($name );
                $saad->soundex = soundex($name );
                $saad->gid = $feature['properties']['id'];
                //$saad->save();
                $saadGeography = new TheSaadGeography();
                $saadGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
                $saad->save();
                $saad->geography()->save($saadGeography);
            }
        }
    }
}

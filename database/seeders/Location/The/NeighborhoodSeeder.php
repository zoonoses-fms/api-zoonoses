<?php

namespace Database\Seeders\Location\The;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location\The\TheSaad;
use App\Models\Location\The\TheNeighborhood;
use App\Models\Location\The\TheNeighborhoodGeography;

class NeighborhoodSeeder extends Seeder
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
        $path = __DIR__ . '/../../files/the/the_neighborhoods.geojson';
        $file = file_get_contents($path, true);

        if ($file) {
            $fileNeighborhood = json_decode($file, true);

            $features = $fileNeighborhood['features'];

            foreach ($features as $feature) {
                $geometry = json_encode($feature['geometry']);
                $zone = TheSaad::where(
                    'standardized',
                    $this->nameCase($feature['properties']['regiao'])
                )->first();
                $neighborhood = new TheNeighborhood();

                $neighborhood->name = $feature['properties']['nome'];
                $neighborhood->standardized = $this->nameCase($feature['properties']['nome']);
                $neighborhood->metaphone = $neighborhood->getPhraseMetaphone($feature['properties']['nome']);
                $neighborhood->soundex = soundex($feature['properties']['nome']);

                $neighborhood->gid = $feature['properties']['codigo'];
                $neighborhood->sinan_code = $feature['properties']['sinan_codigo'];
                $neighborhood->the_saad_id = $zone->id;

                $neighborhoodGeography = new TheNeighborhoodGeography();
                $neighborhoodGeography->area = DB::raw("ST_GeomFromGeoJSON('{$geometry}')");
                $neighborhood->save();
                $neighborhood->geography()->save($neighborhoodGeography);
            }
        }

        $path_urban_center = __DIR__ . '/../../files/the/the_urban_centers.geojson';
        $file_urban_center = file_get_contents($path_urban_center, true);

        if ($file_urban_center) {
            $fileUrbanCenter = json_decode($file_urban_center, true);

            $features = $fileUrbanCenter['features'];

            foreach ($features as $feature) {
                $geometry = json_encode($feature['geometry']);
                $zone = TheSaad::where(
                    'standardized',
                    $this->nameCase($feature['properties']['regiao'])
                )->first();
                $neighborhood = new TheNeighborhood();

                $neighborhood->name = $feature['properties']['nome'];
                $neighborhood->standardized = $this->nameCase($feature['properties']['nome']);
                $neighborhood->metaphone = $neighborhood->getPhraseMetaphone($feature['properties']['nome']);
                $neighborhood->soundex = soundex($feature['properties']['nome']);

                $neighborhood->gid = $feature['properties']['codigo'];
                $neighborhood->the_saad_id = $zone->id;

                $neighborhoodGeography = new TheNeighborhoodGeography();
                $neighborhoodGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
                $neighborhood->save();
                $neighborhood->geography()->save($neighborhoodGeography);
            }
        }
    }
}

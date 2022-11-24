<?php

namespace App\Models\Datasus\Dengueon\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Location\The\TheNeighborhood;
use App\Models\Location\The\TheNeighborhoodAlias;
use App\Models\Datasus\Dengueon\DengueonDatasus;
use App\Models\Dataset;

class TheDengueonDatasus extends DengueonDatasus
{
    use HasFactory;

    public $keys = ['nu_notific', 'dt_notific'];

    public $ibgeCodeCity = '2211001';
    public $ibgeCodeCityShot = '221100';
    public $colIbgeCode = 'id_mn_resi';
    public $colNeighborhoodName = 'nm_bairro';
    public $colNeighborhoodId = 'id_bairro';

    public function filterIsResident($query)
    {
        return $query->orWhere(
            'id_mn_resi',
            $this->ibgeCodeCity
        )->orWhere(
            'id_mn_resi',
            $this->ibgeCodeCityShot
        );
    }

    public function createWhere($query, $request, $per = 'id')
    {
        $query->when(
            $request->has('is_resident') && ($request->get('is_resident') === "true"),
            function ($query) {
                return $this->filterIsResident($query);
            }
        );

        return parent::createWhere($query, $request, $per);
    }

    public function getTheNeighborhood($alias)
    {
        $neighborhood = null;

        $neighborhood = TheNeighborhood::where('standardized', 'ilike', "%{$alias->standardized}%")->first();

        if (!is_null($neighborhood)) {
            return $neighborhood->id;
        }

        if (is_null($neighborhood)) {
            $neighborhood = TheNeighborhood::where('metaphone', 'ilike', "%{$alias->metaphone}%")->first();

            if (!is_null($neighborhood)) {
                return $neighborhood->id;
            }
        }

        if (is_null($neighborhood)) {
            $neighborhood = TheNeighborhood::where('soundex', 'ilike', "%{$alias->soundex}%")->first();

            if (!is_null($neighborhood)) {
                return $neighborhood->id;
            }
        }

        return null;
    }

    public function rowHandler($row)
    {
        if ($row[$this->colIbgeCode] == $this->ibgeCodeCityShot) {
            if (!is_null($row[$this->colNeighborhoodName])) {
                $neighborhoodAlias = new TheNeighborhoodAlias();
                $alias = null;

                $standardized = $neighborhoodAlias->nameCase($row[$this->colNeighborhoodName]);
                $metaphone = $neighborhoodAlias->getPhraseMetaphone($row[$this->colNeighborhoodName]);
                $soundex = soundex($row[$this->colNeighborhoodName]);

                $alias = TheNeighborhoodAlias::where('standardized', 'ilike', "%{$standardized}%")->first();

                if (is_null($alias)) {
                    $alias = TheNeighborhoodAlias::where('metaphone', 'ilike', "%{$metaphone}%")->first();
                }

                if (is_null($alias)) {
                    $alias = TheNeighborhoodAlias::where('soundex', 'ilike', "%{$soundex}%")->first();
                }

                if (is_null($alias)) {
                    $alias = new TheNeighborhoodAlias();
                    $alias->name = $row[$this->colNeighborhoodName];
                    $alias->standardized = $standardized;
                    $alias->metaphone = $metaphone;
                    $alias->soundex = $soundex;
                    $alias->the_neighborhood_id = $this->getTheNeighborhood($alias);
                    $alias->save();
                }

                $row[$this->colNeighborhoodId] = $alias->id;
                return $row;
            } else {
                $row[$this->colNeighborhoodId] = null;
                return $row;
            }
        } else {
            return $row;
        }
    }

    public static function getClassNeighborhood($initial)
    {
        $class = 'App\Models\Location\\';
        $class .= ucfirst($initial) . '\\';
        $class .= ucfirst($initial);
        $class .= 'Neighborhood';

        return $class;
    }

    public static function getClassNeighborhoodSpellingVariation($initial)
    {
        $class = 'App\Models\Location\\';
        $class .= ucfirst($initial) . '\\';
        $class .= ucfirst($initial);
        $class .= 'NeighborhoodSpellingVariation';

        return $class;
    }

    public function getSerieByLocationType(Request $request, $id)
    {
        // neighborhood
        $byLocationType = $request->get('by_location_type');

        $per = $request->get('per');
        $dataset = DataSet::find($id);
        $operation = $request->get('operation');
        $rating = $request->get('rating');
        $year = $dataset->year;
        $initial = $dataset->initial;
        $system = $dataset->system;
        $source = $dataset->source;

        $classNeighborhood =
            $this->getClassNeighborhood($initial);

        $classNeighborhoodSpellingVariation =
            $this->getClassNeighborhoodSpellingVariation($initial);

        $neighborhoods = $classNeighborhood::get();

        foreach ($neighborhoods as $neighborhood) {
            $spellingVariations =
                $classNeighborhoodSpellingVariation::select('id')->where(
                    'the_neighborhood_id',
                    $neighborhood->id
                )->get();
            $ids = [];
            foreach ($spellingVariations as $spelling) {
                $ids[] = $spelling->id;
            }

            $count = DB::table("{$year}_{$initial}_{$system}_{$source}")
            ->whereIn($per, $ids)
            ->where(function ($query) use ($request, $per) {
                return $this->createWhere($query, $request, $per);
            })
            ->groupBy("{$per}")
            ->orderBy("{$per}")
            ->count();
            $neighborhood->ibge_id = $neighborhood->id;
            $neighborhood->idsSpellings = $ids;
            $neighborhood->count = $count;
        }


        return $neighborhoods;

        // return $locations;
    }
}

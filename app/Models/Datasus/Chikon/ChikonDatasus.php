<?php

namespace App\Models\Datasus\Chikon;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Dataset;

class ChikonDatasus extends Dataset
{
    use HasFactory;

    public $sexo = [
        'Masculino' => [['\'1\'', '\'M\'']],
        'Feminino' => [['\'2\'', '\'F\'']],
        'Não consta' => [[null, '\'I\'']],
    ];


    public $keys = ['nu_notific', 'dt_notific'];
    public $col_date_dataset = 'dt_notific';
    public $col_date_dataset_format = 'Ymd';
    public $format_date = 'Ymd';
    public $prefix = 'dengon';
    public $alias = 'Notificação de Chikungunya';

    protected $table = 'datasets';

    public function getSerie(Request $request, $id)
    {
        $per_page = 12;
        if ($request->has('per_page')) {
            $per_page = $request->get('per_page');
        }

        if ($request->has('per') && $request->has('operation')) {
            $per = $request->get('per');

            if ($per == 'dt_notific') {
                return $this->getSerieByDate($request, $id);
            } elseif ($per == 'codestab') {
                return $this->getSerieCnes($request, $id);
            } elseif ($per == 'codocupmae') {
                return $this->getSerieCbo($request, $id);
            } elseif ($request->has('by_location_type')) {
                return $this->getSerieByLocationType($request, $id);
            } else {
                return $this->getSeriePer($request, $id);
            }
        }
    }

    public function getRange(Request $request, $id)
    {
        return $this->getSerieRange($request, $id);
    }
}

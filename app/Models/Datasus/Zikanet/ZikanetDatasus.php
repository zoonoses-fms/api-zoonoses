<?php

namespace App\Models\Datasus\Zikanet;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Dataset;
use Exception;
use Throwable;

class ZikanetDatasus extends Dataset
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
    public $alias = 'Notificação de Zika';
    public $cid = 'A928';
    public $ibgeCodeCityShot = '221100';

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

    //carregar dados
    public function loadFileDbf($path, $source, $system, $initial, $user)
    {
        $currentYear = null;

        $db = dbase_open(storage_path('app/' . $path), 0);

        if ($db) {
            $num_rows = dbase_numrecords($db);
            $tablesDataSets = [];

            $columns = dbase_get_header_info($db);
            $keyColumns = $this->keys;

            foreach ($columns as $index => $column) {
                $column['name'] = strtolower($column['name']);
                $column['name']  = $this->clearKeyAndField($column['name']);

                foreach ($keyColumns as $key) {
                    if ($column['name'] == $key) {
                        unset($columns[$index]);
                        break;
                    }
                }
            }

            foreach ($columns as $column) {
                $updateColumns[] = strtolower($column['name']);
            }

            try {
                # Loop the Dbase records
                for ($index = 1; $index <= $num_rows; $index++) {
                    # Get one record
                    $record = dbase_get_record_with_names($db, $index);
                    # Ignore deleted fields
                    if (
                        $record['deleted'] != '1' && trim($record["ID_AGRAVO"]) == $this->cid &&
                        trim($record["ID_MN_RESI"]) == $this->ibgeCodeCityShot
                    ) {
                        $record = array_change_key_case($record, CASE_LOWER);

                        $year = $this->getYear($record);

                        if ($year != $currentYear) {
                            $table = $this->changeCurrentTable(
                                $year,
                                $path,
                                $source,
                                $system,
                                $initial,
                                $user,
                                $keyColumns
                            );
                            $currentYear = $year;
                        }

                        foreach ($record as $key => $field) {
                            if ($key !== 'deleted') {
                                try {
                                    $field = $this->clearKeyAndField($field);
                                    $key = $this->clearKeyAndField($key);

                                    $data[$key] = $this->formatField($key, $field);
                                } catch (Throwable $e) {
                                    echo $e->getMessage();
                                    throw $e;
                                }
                            }
                        }

                        $data = $this->rowHandler($data);

                        $data['created_at'] = date('Y-m-d H:i:s');
                        $data['updated_at'] = date('Y-m-d H:i:s');

                        $tablesDataSets[$table->tableName][] = $data;
                    }
                }

                foreach ($tablesDataSets as $tableName => $dataset) {
                    $this->upsert(
                        $tableName,
                        $dataset,
                        $keyColumns,
                        $updateColumns
                    );
                }
                dbase_close($db);
                // DB::commit();
            } catch (Exception $e) {
                throw $e;
            }
        }
        return true;
    }
}

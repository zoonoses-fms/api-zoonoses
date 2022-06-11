<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpParser\Node\Stmt\TryCatch;
use Throwable;

class DatasetSihController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $source, $initial)
    {
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        } else {
            $limit = 20;
        }

        $dataset_rds = Dataset::where(
            [
                'source' => $source,
                'system' => 'sihrd',
                'initial' => $initial,
            ]
        )->limit($limit)->orderBy('year', 'desc')->get();

        $dataset_rjs = Dataset::where(
            [
                'source' => $source,
                'system' => 'sihrj',
                'initial' => $initial,
            ]
        )->limit($limit)->orderBy('year', 'desc')->get();

        $rd = [];
        $rj = [];

        foreach ($dataset_rds as $dataset_rd) {
            $rd[$dataset_rd->year] = $dataset_rd;
        }
        foreach ($dataset_rjs as $dataset_rj) {
            $rj[$dataset_rj->year] = $dataset_rj;
        }

        $intersects = array_intersect_key($rd, $rj);

        $class = Dataset::getClass($source, 'sih', $initial);

        $object = new $class();
        $datasets = [];

        foreach ($intersects as $dataset) {
            $dataset->approved = $object->getTotal($request, $source, 'sihrd', $initial, $dataset->id);
            $dataset->rejected = $object->getTotal($request, $source, 'sihrj', $initial, $dataset->id);
            $dataset->total = $dataset->approved + $dataset->rejected;
            $datasets[] = $dataset;
        }

        return $datasets;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dataset  $dataset
     * @return \Illuminate\Http\Response
     */
    public function showYear(Request $request, $source, $system, $initial, $year)
    {


        $dataset_rd = Dataset::where(
            [
                'source' => $source,
                'system' => 'sihrd',
                'initial' => $initial,
                'year' => $year,
            ]
        )->first();

        $dataset_rj = Dataset::where(
            [
                'source' => $source,
                'system' => 'sihrj',
                'initial' => $initial,
                'year' => $year,
            ]
        )->first();

        if ($dataset_rd == null || $dataset_rj == null) {
            return $this->error('Not dataset!', 503);
        }

        $class = Dataset::getClass($source, 'sih', $initial);

        $object = new $class();

        $dataset = $dataset_rd;

        $dataset->approved = $object->getTotal($request, $source, 'sihrd', $initial, $dataset_rd->id);
        $dataset->rejected = $object->getTotal($request, $source, 'sihrj', $initial, $dataset_rj->id);
        $dataset->total = $dataset->approved + $dataset->rejected;

        return $dataset;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSerie(Request $request, $source, $initial, $id)
    {
        $class = DataSet::getClass($source, 'sih', $initial);

        $object = new $class();

        return $object->getSerie($request, $id);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRange(Request $request, $source, $initial, $id)
    {
        $class = DataSet::getClass($source, 'sih', $initial);

        $object = new $class();

        return $object->getRange($request, $id);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDataTable(Request $request, $source, $initial, $id)
    {
        $class = DataSet::getClass($source, 'sih', $initial);

        $object = new $class();

        return $object->getDataTable($request, $id);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDataMap(Request $request, $source, $initial, $id)
    {
        $class = DataSet::getClass($source, 'sih', $initial);

        $object = new $class();

        return $object->getDataMap($request, $id);
    }
}

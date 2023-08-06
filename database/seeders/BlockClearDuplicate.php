<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Location\The\TheBlockGeography;
use App\Models\Location\The\TheBlock;

class BlockClearDuplicate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $duplicate =
            TheBlockGeography::select(
                DB::raw('count(*) as count, area')
            )
                ->groupBy('area')
                ->havingRaw('count(*) > ?', [1])
                ->get();

        foreach ($duplicate as $block) {
            echo($block->count);
            echo(":");
            echo($block->area);
            echo("\n");

            $itemsDuplicates = TheBlock::whereHas('geography', function ($q) use ($block) {
                $q->where('area', $block->area);
            })->get();

            for ($i=1; $i < count($itemsDuplicates); $i++) {
                $itemsDuplicates[$i]->geography->delete();
                $itemsDuplicates[$i]->delete();
            }
        }
    }
}

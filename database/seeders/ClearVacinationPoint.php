<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;

class ClearVacinationPoint extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $c = Campaign::orderBy('id', 'desc')->first();

        foreach ($c->cycles as $cycle) {
            foreach ($cycle->supports as $support) {
                foreach ($support->points as $point) {
                    $point->bottle_lost = 0;
                    $point->bottle_received = 0;
                    $point->bottle_returned_completely = 0;
                    $point->bottle_returned_partially = 0;
                    $point->bottle_used_completely = 0;
                    $point->bottle_used_partially = 0;

                    $point->female_cat = 0;
                    $point->male_cat = 0;
                    $point->total_of_cats = 0;

                    $point->female_dog_under_4m = 0;
                    $point->female_dog_major_4m_under_1y = 0;
                    $point->female_dog_major_1y_under_2y = 0;
                    $point->female_dog_major_2y_under_4y = 0;
                    $point->female_dog_major_4y = 0;
                    $point->female_dogs = 0;

                    $point->male_dog_under_4m = 0;
                    $point->male_dog_major_4m_under_1y = 0;
                    $point->male_dog_major_1y_under_2y = 0;
                    $point->male_dog_major_2y_under_4y = 0;
                    $point->male_dog_major_4y = 0;
                    $point->male_dogs = 0;
                    $point->total_of_dogs = 0;

                    $point->total = 0;
                    $point->save();
                }
            }
        }
    }
}

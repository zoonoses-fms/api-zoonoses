<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year',
        'start',
        'end',
        'goal',
        'coordinator_cost',
        'supervisor_cost',
        'assistant_cost',
        'vaccinator_cost',
        'annotators_cost',
        'rural_supervisor_cost',
        'rural_assistant_cost',
        'vaccine_cost',
        'mileage_cost',
        'driver_cost',
        'coordinator_id'
    ];

    public function cycles()
    {
        return $this->hasMany(CampaignCycle::class, 'campaign_id')->orderBy('number', 'asc');
    }

    public static function buildItem($item)
    {
        $item->male_dog_under_4m = 0;
        $item->female_dog_under_4m = 0;

        $item->male_dog_major_4m_under_1y = 0;
        $item->female_dog_major_4m_under_1y = 0;

        $item->male_dog_major_1y_under_2y = 0;
        $item->female_dog_major_1y_under_2y = 0;

        $item->male_dog_major_2y_under_4y = 0;
        $item->female_dog_major_2y_under_4y = 0;

        $item->male_dog_major_4y = 0;
        $item->female_dog_major_4y = 0;

        $item->male_dogs = 0;
        $item->female_dogs = 0;

        $item->total_of_dogs = 0;

        $item->male_cat = 0;
        $item->female_cat = 0;

        $item->total_of_cats = 0;
        $item->total = 0;
        $item->goal = 0;
    }

    public static function incrementItem($item, $increment) {
        $item->male_dog_under_4m += $increment->male_dog_under_4m;
        $item->female_dog_under_4m += $increment->female_dog_under_4m;

        $item->male_dog_major_4m_under_1y += $increment->male_dog_major_4m_under_1y;
        $item->female_dog_major_4m_under_1y += $increment->female_dog_major_4m_under_1y;

        $item->male_dog_major_1y_under_2y += $increment->male_dog_major_1y_under_2y;
        $item->female_dog_major_1y_under_2y += $increment->female_dog_major_1y_under_2y;

        $item->male_dog_major_2y_under_4y += $increment->male_dog_major_2y_under_4y;
        $item->female_dog_major_2y_under_4y += $increment->female_dog_major_2y_under_4y;

        $item->male_dog_major_4y += $increment->male_dog_major_4y;
        $item->female_dog_major_4y += $increment->female_dog_major_4y;

        $item->male_dogs += $increment->male_dogs;
        $item->female_dogs += $increment->female_dogs;

        $item->total_of_dogs += $increment->total_of_dogs;

        $item->male_cat += $increment->male_cat;
        $item->female_cat += $increment->female_cat;

        $item->total_of_cats += $increment->total_of_cats;
        $item->total += $increment->total;
        $item->goal += $increment->goal;
    }
}

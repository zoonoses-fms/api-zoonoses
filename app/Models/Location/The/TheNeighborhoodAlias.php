<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MetaPhone;

class TheNeighborhoodAlias extends MetaPhone
{
    use HasFactory;

    public function neighborhood()
    {
        return $this->belongsTo(TheNeighborhood::class, 'the_neighborhood_id');
    }

    public static function createByName($name)
    {
        $alias = new self();

        $alias->name = $name;
        $alias->standardized = $alias->nameCase($name);
        $alias->metaphone = $alias->getPhraseMetaphone($name);
        $alias->soundex = soundex($name);

        $neighborhood = TheNeighborhood::getByName($name);

        if ($neighborhood != null) {
            $alias->the_neighborhood_id = $neighborhood->id;
        }

        $alias->save();

        return $alias;
    }

    public static function getOrCreate($name)
    {
        $alias = self::getByEqualsName($name);

        if ($alias === null) {
            $alias = self::createByName($name);
        }

        return $alias;
    }
}

<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MetaPhone;

class TheNeighborhoodSpellingVariation extends MetaPhone
{
    use HasFactory;

    public function neighborhood()
    {
        return $this->belongsTo(TheNeighborhood::class);
    }

    public static function createByName($name)
    {
        $spellingVariation = new self();

        $spellingVariation->name = $name;
        $spellingVariation->standardized = $spellingVariation->nameCase($name);
        $spellingVariation->metaphone = $spellingVariation->getPhraseMetaphone($name);
        $spellingVariation->soundex = soundex($name);

        $neighborhood = TheNeighborhood::getByName($name);

        if($neighborhood != null) {
            $spellingVariation->the_neighborhood_id = $neighborhood->id;
        }

        $spellingVariation->save();

        return $spellingVariation;
    }

    public static function getOrCreate($name)
    {
        $spellingVariation = self::getByName($name);

        if ($spellingVariation === null) {
            $spellingVariation = self::createByName($name);
        }

        return $spellingVariation;

    }
}

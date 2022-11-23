<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaingPoint extends Model
{
    use HasFactory;

    public function support()
    {
        return $this->belongsTo(CampaingSupport::class, 'campaing_support_id')->orderBy('name', 'asc');
    }

    public function supervisor()
    {
        return $this->belongsTo(VaccinationWorker::class, 'supervisor_id')->orderBy('name', 'asc');
    }

    public function vaccinators()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'vaccinator_point',
            'campaing_point_id',
            'vaccinator_id'
        )->orderBy('name', 'asc');
    }

    public function annotators()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'annotator_point',
            'campaing_point_id',
            'annotator_id'
        )->orderBy('name', 'asc');
    }

    public function saads()
    {
        return $this->belongsToMany(
            TheSaad::class,
            'saad_point',
            'campaing_point_id',
            'saad_id'
        );
    }

    public function point()
    {
        return $this->belongsTo(VaccinationPoint::class, 'vaccination_point_id');
    }
}

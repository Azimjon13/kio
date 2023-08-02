<?php

namespace App\Kio\Places\Shifts;

use App\Kio\Places\PlaceModel;
use App\Kio\Technicas\TechnicaTypeModel;
use Illuminate\Database\Eloquent\Model;

class PlaceShiftModel extends Model
{
    protected $table = 'places_shifts';
    protected $guarded = ['id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function technica_type(){
        return $this->belongsTo(TechnicaTypeModel::class, 'id_technica_type', 'id');
    }
}

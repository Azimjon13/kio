<?php

namespace App\Kio\Excavators;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExcavatorPlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places_excavators';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function excavator(){
        return $this->belongsTo(ExcavatorModel::class, 'id_excavator', 'id');
    }

}

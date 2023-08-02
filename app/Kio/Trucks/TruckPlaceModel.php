<?php

namespace App\Kio\Trucks;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckPlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places_trucks';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function truck(){
        return $this->belongsTo(TruckModel::class, 'id_truck', 'id');
    }

}

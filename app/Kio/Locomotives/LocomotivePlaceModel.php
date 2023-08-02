<?php

namespace App\Kio\Locomotives;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocomotivePlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places_locomotives';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function locomotive(){
        return $this->belongsTo(LocomotiveModel::class, 'id_locomotive', 'id');
    }

}

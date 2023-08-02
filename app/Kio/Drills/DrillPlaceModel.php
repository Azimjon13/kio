<?php

namespace App\Kio\Drills;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrillPlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places_drills';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function drill(){
        return $this->belongsTo(DrillModel::class, 'id_drill', 'id');
    }

}

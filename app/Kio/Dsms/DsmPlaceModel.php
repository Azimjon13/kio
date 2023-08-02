<?php

namespace App\Kio\Dsms;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DsmPlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places_dsms';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function dsm(){
        return $this->belongsTo(DsmModel::class, 'id_dsm', 'id');
    }

}

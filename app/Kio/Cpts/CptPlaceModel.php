<?php

namespace App\Kio\Cpts;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CptPlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places_cpts';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function cpt(){
        return $this->belongsTo(CptModel::class, 'id_cpt', 'id');
    }

}

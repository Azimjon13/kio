<?php

namespace App\Kio\Excavators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExcavatorModel extends Model
{
    use SoftDeletes;
    protected $table = 'excavators';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function excavator_places()
    {
        return $this->hasMany(ExcavatorPlaceModel::class, 'id_excavator', 'id');
    }

    public function latest_excavator_place()
    {
        return $this->hasOne(ExcavatorPlaceModel::class, 'id_excavator', 'id')->ofMany('date_from', 'max');
    }

}

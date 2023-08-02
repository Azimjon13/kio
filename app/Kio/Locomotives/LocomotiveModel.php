<?php

namespace App\Kio\Locomotives;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocomotiveModel extends Model
{
    use SoftDeletes;
    protected $table = 'locomotives';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function locomotive_places()
    {
        return $this->hasMany(LocomotivePlaceModel::class, 'id_locomotive', 'id');
    }

    public function latest_locomotive_place()
    {
        return $this->hasOne(LocomotivePlaceModel::class, 'id_locomotive', 'id')->ofMany('date_from', 'max');
    }

}

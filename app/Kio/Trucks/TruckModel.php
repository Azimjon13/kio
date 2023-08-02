<?php

namespace App\Kio\Trucks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckModel extends Model
{
    use SoftDeletes;
    protected $table = 'trucks';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function truck_places()
    {
        return $this->hasMany(TruckPlaceModel::class, 'id_truck', 'id');
    }

    public function latest_truck_place()
    {
        return $this->hasOne(TruckPlaceModel::class, 'id_truck', 'id')->ofMany('date_from', 'max');
    }

}

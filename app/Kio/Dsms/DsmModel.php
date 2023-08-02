<?php

namespace App\Kio\Dsms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DsmModel extends Model
{
    use SoftDeletes;
    protected $table = 'dsms';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function dsm_places()
    {
        return $this->hasMany(DsmPlaceModel::class, 'id_dsm', 'id');
    }

    public function latest_dsm_place()
    {
        return $this->hasOne(DsmPlaceModel::class, 'id_dsm', 'id')->ofMany('date_from', 'max');
    }

}

<?php

namespace App\Kio\Cpts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CptModel extends Model
{
    use SoftDeletes;
    protected $table = 'cpts';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function cpt_places()
    {
        return $this->hasMany(CptPlaceModel::class, 'id_cpt', 'id');
    }

    public function latest_cpt_place()
    {
        return $this->hasOne(CptPlaceModel::class, 'id_cpt', 'id')->ofMany('date_from', 'max');
    }

}

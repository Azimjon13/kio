<?php

namespace App\Kio\Drills;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrillModel extends Model
{
    use SoftDeletes;
    protected $table = 'drills';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'creator_id',  'deleted_at', 'deleter_id',  'updated_at', 'updater_id'];

    public function drill_places()
    {
        return $this->hasMany(DrillPlaceModel::class, 'id_drill', 'id');
    }

    public function latest_drill_place()
    {
        return $this->hasOne(DrillPlaceModel::class, 'id_drill', 'id')->ofMany('date_from', 'max');
    }

}

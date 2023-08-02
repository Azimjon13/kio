<?php

namespace App\Kio\Places;

use App\Kio\Managers\ManagerModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'places';
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function manager(){
        return $this->belongsTo(ManagerModel::class, 'id_manager', 'id');
    }

    public function places(){
        return $this->hasMany(self::class, 'id_parent', 'id')->orderBy('sort');;
    }

    public function parent(){
        return $this->belongsTo(self::class, 'id_parent','id');
    }
}

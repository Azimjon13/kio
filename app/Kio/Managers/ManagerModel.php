<?php

namespace App\Kio\Managers;

use App\Kio\Places\PlaceModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManagerModel extends Model
{
    use SoftDeletes;
    protected $table = 'managers';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    public $timestamps = false;

    public function places(){
        return $this->hasMany(PlaceModel::class, 'id_manager', 'id')->orderBy('sort');
    }
}

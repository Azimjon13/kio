<?php

namespace App\Kio\Managers;

use App\Kio\Places\PlaceModel;
use App\Laravue\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPlaceModel extends Model
{
    use SoftDeletes;
    protected $table = 'user_places';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }
}

<?php

namespace App\Kio\Technicas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicaTypeModel extends Model
{
    use SoftDeletes;
    protected $table = 'technica_types';
    protected $guarded = ['id'];
    protected $hidden = ['id_category', 'deleted_at'];
    public $timestamps = false;

    public function technica_groups(){
        return $this->hasMany(TechnicaGroupModel::class, 'id_technica_type', 'id');
    }
}

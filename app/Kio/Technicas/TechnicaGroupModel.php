<?php

namespace App\Kio\Technicas;

use App\Kio\Reasons\CodeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicaGroupModel extends Model
{
    use SoftDeletes;
    protected $table = 'technica_groups';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    public $timestamps = false;

    public function technica_type(){
        return $this->belongsTo(TechnicaTypeModel::class, 'id_technica_type', 'id');
    }

    public function codes(){
        return $this->hasMany(CodeModel::class, 'id_group', 'id');
    }
}

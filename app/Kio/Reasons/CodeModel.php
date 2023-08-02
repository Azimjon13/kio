<?php

namespace App\Kio\Reasons;

use App\Kio\Technicas\TechnicaGroupModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeModel extends Model
{
    use SoftDeletes;
    protected $table = 'codes';
    protected $guarded = ['id'];
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public function reason(){
        return $this->belongsTo(ReasonModel::class, 'id_reason', 'id');
    }

    public function technica_group(){
        return $this->belongsTo(TechnicaGroupModel::class, 'id_technica_group', 'id');
    }

    public function downtime_codes(){
        return $this->hasMany(ReportDowntimeCodeModel::class, 'id_code', 'id');
    }
}

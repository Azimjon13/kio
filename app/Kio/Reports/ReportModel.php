<?php

namespace App\Kio\Reports;

use App\Kio\Places\PlaceModel;
use App\Kio\Places\Shifts\PlaceShiftModel;
use App\Kio\Technicas\TechnicaTypeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportModel extends Model
{
    use SoftDeletes;
    protected $table = 'reports';
    protected $guarded = ['id'];
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function technica_type(){
        return $this->belongsTo(TechnicaTypeModel::class, 'id_technica_type', 'id');
    }

    public function downtime_codes(){
        return $this->hasMany(ReportDowntimeCodeModel::class, 'id_report', 'id');
    }

}

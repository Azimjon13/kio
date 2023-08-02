<?php

namespace App\Kio\Reports;

use App\Kio\Reasons\CodeModel;
use Illuminate\Database\Eloquent\Model;

class ReportDowntimeCodeModel extends Model
{
    protected $table = 'reports_downtimes_codes';
    protected $guarded = ['id'];
    public $timestamps = false;
//    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public function report(){
        return $this->belongsTo(ReportModel::class, 'id_report', 'id');
    }

    public function code(){
        return $this->belongsTo(CodeModel::class, 'id_code', 'id');
    }
}

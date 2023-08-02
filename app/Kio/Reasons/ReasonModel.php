<?php

namespace App\Kio\Reasons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReasonModel extends Model
{
    use SoftDeletes;
    protected $table = 'reasons';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public function codes(){
        return $this->hasMany(CodeModel::class, 'id_reason', 'id');
    }
}

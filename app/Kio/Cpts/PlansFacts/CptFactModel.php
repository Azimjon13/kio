<?php

namespace App\Kio\Cpts\PlansFacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CptFactModel extends Model
{
    use SoftDeletes;
    protected $table = 'facts_cpts';
    protected $guarded = ['id'];
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public static function excluded_facts_inmonth_inyear($date_day, $id_place){
        $date_today = new Carbon($date_day);
        $date_month_start = new Carbon($date_day); $date_month_start->startOfMonth();
        $date_month_end = new Carbon($date_day); $date_month_end->endOfMonth();
        return DB::select("SELECT
            SUM((CASE
                    WHEN f.date_fact BETWEEN DATE(:month_start) AND DATE(:month_end)
                        THEN  f.fact_month
                    ELSE NULL
                  END
               )) as 'fact_month',
             SUM(f.fact_year) as 'fact_year'
            FROM facts_cpts f,
            (
                SELECT a.id_place_cpt, MAX(a.date_fact) as 'maxDate'
                FROM facts_cpts a
                WHERE a.deleted_at is null AND a.id_place = :id_place
                GROUP BY a.id_place_cpt
            ) f1
            WHERE f.date_fact = f1.maxDate  AND f.id_place_cpt = f1.id_place_cpt AND f.id_place_cpt in ( SELECT
            pe.id
            FROM places_cpts pe
            WHERE pe.id_place = :id_place AND pe.date_to < DATE(:date_day) AND YEAR(pe.date_to) = YEAR(:date_day) AND pe.deleted_at is null )",
            ["date_day" => $date_today, 'month_start'  => $date_month_start, 'month_end'  => $date_month_end, "id_place" => $id_place]);
    }
}

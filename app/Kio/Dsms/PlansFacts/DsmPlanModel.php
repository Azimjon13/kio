<?php

namespace App\Kio\Dsms\PlansFacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DsmPlanModel extends Model
{
    use SoftDeletes;
    protected $table = 'plans_dsms';
    protected $guarded = ['id'];
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public static function excluded_plans_inmonth_inyear($date_day, $id_place){
        $date_today = new Carbon($date_day);
        $date_month_start = new Carbon($date_day); $date_month_start->startOfMonth();
        $date_month_end = new Carbon($date_day); $date_month_end->endOfMonth();
        return DB::select("SELECT
                 SUM((CASE
                        WHEN p.date_plan BETWEEN DATE(:month_start) AND DATE(:month_end)
                            THEN  p.plan_month
                        ELSE NULL
                      END
                   )) as 'plan_month',
                 SUM(p.plan_year) as 'plan_year'
                FROM plans_dsms p,
                (
                    SELECT a.id_place_dsm, MAX(a.date_plan) as 'maxDate'
                    FROM plans_dsms a
                    WHERE a.deleted_at is null AND a.id_place = :id_place
                    GROUP BY a.id_place_dsm
                ) p1
                WHERE p.date_plan = p1.maxDate  AND p.id_place_dsm = p1.id_place_dsm AND p.id_place_dsm in ( SELECT
                pe.id
                FROM places_dsms pe
                WHERE pe.id_place = :id_place AND pe.date_to < DATE(:date_day) AND YEAR(pe.date_to) = YEAR(:date_day) AND pe.deleted_at is null)",
            ["date_day" => $date_today, 'month_start'  => $date_month_start, 'month_end'  => $date_month_end, "id_place" => $id_place]);
    }
}

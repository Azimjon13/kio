<?php

namespace App\Kio\Drills\Downtimes;

use App\Kio\Drills\DrillModel;
use App\Kio\Drills\DrillPlaceModel;
use App\Kio\Places\Shifts\PlaceShiftModel;
use App\Kio\Reasons\CodeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DrillDowntimeModel extends Model
{
    use SoftDeletes;
    protected $table = 'downtimes_drills';
    protected $guarded = ['id'];
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public function drill(){
        return $this->belongsTo(DrillModel::class, 'id_drill', 'id');
    }

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function code(){
        return $this->belongsTo(CodeModel::class, 'id_code', 'id');
    }

    public function place_drill(){
        return $this->belongsTo(DrillPlaceModel::class, 'id_place_drill', 'id');
    }

    public static function downtime_plan_fact($date_day, $id_place){
        return DB::select("SELECT
            pd.id as 'id_place_drill',
            pd.id_place,
            pd.id_drill,
            e.title,
            pd.garage,
            f.id as 'id_fact',
            p.id as 'id_plan',
            p.plan,
            f.fact,
            p.plan_month,
            f.fact_month,
            p.plan_year,
            f.fact_year
        FROM drills e, places_drills pd
                LEFT JOIN plans_drills p ON
                            pd.id = p.id_place_drill AND
                            p.date_plan = :date_day AND
                            p.deleted_at is null
                LEFT JOIN facts_drills f ON
                            pd.id = f.id_place_drill AND
                            f.date_fact = :date_day AND
                            f.deleted_at is null
        WHERE
                    pd.id_drill = e.id
		AND pd.id_place = :id_place
        AND pd.deleted_at is null
        AND e.deleted_at is null AND
        DATE(:date_day) BETWEEN pd.date_from AND COALESCE(pd.date_to, DATE(:date_day))", ["date_day" => $date_day, "id_place" => $id_place]);
    }

    public static function downtime_daily_by_plan_fact($date_day, $id_place, $id_place_drills){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 2)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_start = new Carbon($date_day); $shift_start->addMinutes($start_difference);
        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrPos = [];
        $arrBind = ["shift_start" => $shift_start, "shift_end"=> $shift_end, "shift_month_start"=> $shift_month_start];
        foreach ($id_place_drills as $pos => $id) {
            $arrPos[] = ":id_{$pos}";
            $arrBind["id_{$pos}"] = $id;
        }
        if(!empty($arrPos)){
            $query_part = " de.id_place_drill IN (".implode(', ', $arrPos).")";
        }else{
            $query_part = " de.id_place_drill IN ( null )";
        }
        return DB::select("SELECT
	r.id as 'id_reason',
    r.reason,
    r.type,
    r.is_reported,
    ROUND(SUM(
    	TIMESTAMPDIFF(SECOND,
                      (CASE
                       	WHEN :shift_start <= de.start_time AND de.start_time  <  :shift_end
                       		THEN de.start_time
                       	WHEN de.start_time < :shift_start  AND ( :shift_start <= de.end_time  OR de.end_time is NULL)
                       		THEN :shift_start
                        ELSE NULL
                       END),
                      (CASE
                       	WHEN :shift_start <= de.end_time  AND de.end_time < :shift_end
                       		THEN de.end_time
                        WHEN de.start_time < :shift_end AND ( :shift_end < de.end_time OR de.end_time is NULL)
                       		THEN :shift_end
                        ELSE NULL
                       END)
                     )
    )/3600, 2) as 'dayDowntime',

    ROUND(SUM(
    	TIMESTAMPDIFF(SECOND,
                      (CASE
                       	WHEN :shift_month_start <= de.start_time AND de.start_time < :shift_end
                       		THEN de.start_time
                      	WHEN de.start_time < :shift_month_start AND (:shift_month_start <= de.end_time OR de.end_time is null )
                            THEN :shift_month_start
                        ELSE NULL
                       END),
                      (CASE
                       	WHEN  :shift_month_start <= de.end_time  AND de.end_time < :shift_end
                       		THEN de.end_time
                      	WHEN de.start_time < :shift_month_start AND (:shift_end < de.end_time OR de.end_time is null)
                       		THEN :shift_end
                        ELSE NULL
                       END)
                     )
    )/3600, 2) as 'sinceDowntime',
    de.id_place_drill,
    de.id_drill

FROM reasons r
		LEFT JOIN  codes c  ON
            c.id_reason = r.id AND
            c.deleted_at is null
		LEFT JOIN downtimes_drills de   ON
        	de.id_code = c.id  AND
        	de.start_time <= :shift_end AND
        	(de.end_time >= :shift_month_start OR de.end_time is null) AND
            ".$query_part." AND
            de.deleted_at is null
WHERE r.deleted_at IS NULL AND r.drill = 1
GROUP BY r.id, r.reason, de.id_place_drill, de.id_drill,   r.type,  r.is_reported", $arrBind);
    }

    public static function downtime_daily($date_day, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 2)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_start = new Carbon($date_day); $shift_start->addMinutes($start_difference);
        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);

        $arrBind = ["date_day" => $date_day, "shift_start" => $shift_start, "shift_end" => $shift_end, "id_place" => $id_place];

        return DB::select("SELECT
	pd.id_drill,
    pd.id_place,
    pd.garage,
    p.title as 'title_place',
    e.title as 'title_drill',
    de.id as 'id_downtime',
    de.id_code,
    de.start_time,
    de.end_time,
    de.details,
    c.code,
    c.title,
    r.id as 'id_reason',
    r.type,
    r.reason,
    (CASE
     	WHEN de.start_time BETWEEN :shift_start AND :shift_end AND de.end_time BETWEEN :shift_start AND :shift_end
     		THEN ROUND(TIMESTAMPDIFF(SECOND, de.start_time, de.end_time)/3600, 2)
     	WHEN de.start_time BETWEEN :shift_start AND :shift_end AND (de.end_time IS NULL OR :shift_end < de.end_time)
     		THEN ROUND(TIMESTAMPDIFF(SECOND, de.start_time, :shift_end)/3600 , 2)
     	WHEN de.end_time BETWEEN :shift_start AND :shift_end AND de.start_time < :shift_start
     		THEN ROUND(TIMESTAMPDIFF(SECOND, :shift_start, de.end_time)/3600, 2)
     	WHEN de.start_time < :shift_start AND :shift_end < de.end_time
     		THEN 24
     	ELSE NULL
    END) as 'dayDuration',

    (CASE
     	WHEN de.start_time BETWEEN :shift_start AND :shift_end AND de.end_time BETWEEN :shift_start AND :shift_end
     		THEN ROUND(TIMESTAMPDIFF(SECOND, de.start_time, de.end_time)/3600, 2)
    	WHEN de.start_time BETWEEN :shift_start AND :shift_end AND (de.end_time IS NULL OR :shift_end < de.end_time)
     		THEN ROUND(TIMESTAMPDIFF(SECOND, de.start_time, :shift_end)/3600 , 2)
    	WHEN de.end_time BETWEEN :shift_start AND :shift_end AND de.start_time < :shift_start
     		THEN ROUND(TIMESTAMPDIFF(SECOND, :shift_start, de.end_time)/3600, 2)
     	WHEN de.start_time < :shift_start AND :shift_end < de.end_time
     		THEN ROUND(TIMESTAMPDIFF(SECOND, de.start_time, :shift_end)/3600, 2)
     	ELSE NULL
    END)  as 'sinceDuration'
FROM  drills e, places p, places_drills pd, downtimes_drills de, codes c, reasons r
WHERE
	pd.id_drill = de.id_drill AND
    pd.id_place = de.id_place AND
    de.id_code = c.id AND
    c.id_reason = r.id AND
    pd.id_place = p.id AND pd.id_drill = e.id AND
    pd.date_from <= DATE(:date_day) AND
    ( pd.date_to >= DATE(:date_day)  OR pd.date_to IS NULL ) AND
    pd.id_place = :id_place AND
    de.start_time < :shift_end AND
    ( de.end_time > :shift_start OR de.end_time is null ) AND
    de.deleted_at is null AND
    e.deleted_at is null AND
    p.deleted_at is null AND
    pd.deleted_at is null AND
    c.deleted_at is null AND
    r.deleted_at is null AND
    r.drill = 1

ORDER BY de.start_time", $arrBind);
    }

    public static function downtime_exluded($date_day, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 2)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);
        $shift_month_end = new Carbon($date_day); $shift_month_end->endOfMonth()->addMinutes($start_difference);

        $arrBind = ["shift_end" => $shift_end, "shift_month_start" => $shift_month_start, "id_place" => $id_place];

        return DB::select("SELECT
	r.id as 'id_reason',
    r.reason,
    r.type,
    r.is_reported,

    ROUND(SUM(
    	TIMESTAMPDIFF(SECOND,
                      (CASE
                       	WHEN :shift_month_start <= de.start_time AND de.start_time < :shift_end
                       		THEN de.start_time
                      	WHEN de.start_time < :shift_month_start AND (:shift_month_start <= de.end_time OR de.end_time is null )
                            THEN :shift_month_start
                        ELSE NULL
                       END),
                      (CASE
                       	WHEN  :shift_month_start <= de.end_time  AND de.end_time < :shift_end
                       		THEN de.end_time
                      	WHEN de.start_time < :shift_end AND (:shift_end < de.end_time OR de.end_time is null)
                       		THEN :shift_end
                        ELSE NULL
                       END)
                     )
    )/3600, 2) as 'excludedDowntime'

FROM reasons r
		LEFT JOIN  codes c  ON
            c.id_reason = r.id AND
            c.deleted_at is null
		LEFT JOIN downtimes_drills de   ON
        	de.id_code = c.id  AND
        	de.start_time <= :shift_end AND
        	(de.end_time >= :shift_month_start OR de.end_time is null) AND
            de.id_place = :id_place AND
            de.deleted_at is null
WHERE r.deleted_at IS NULL AND r.drill = 1
GROUP BY r.id, r.reason,  r.type,  r.is_reported", $arrBind);
    }

    public static function downtime_exluded_hours($date_day, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 2)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrBind = ["shift_month_start" => $shift_month_start, "shift_end" => $shift_end, "id_place" => $id_place];
        return DB::select("SELECT
            SUM(DAY(p.date_to)*24) as 'addingHours'
            FROM places_drills p
            WHERE p.deleted_at is null and p.id_place = :id_place AND
            :shift_month_start <= p.date_to  AND  p.date_to < :shift_end ", $arrBind);
    }

    public static function downtime_by_drill($date_day, $id_place_drill){
        $id_place = DrillPlaceModel::find($id_place_drill)->id_place;
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 2)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrBind = ["shift_month_start" => $shift_month_start, "shift_end" => $shift_end, "id_place_drill" => $id_place_drill];
        return DB::select("SELECT
pd.id as 'id_place_drill',
pd.id_place as 'id_place',
pd.id_drill as 'id_drill',
r.reason,
r.type,
r.id as 'id_reason',
c.id as 'id_code',
c.code as 'code',
c.title as 'title_code',
pd.garage as 'garage',
de.start_time,
de.end_time,
ROUND(TIMESTAMPDIFF(SECOND,
              (
                  CASE
                  	WHEN de.start_time < :shift_month_start
                  		THEN :shift_month_start
                    ELSE de.start_time
                  END
               ),
               (
                   CASE
                   		WHEN de.end_time > :shift_end
                   			THEN :shift_end
                  		ELSE de.end_time
                   END
               )
              )/3600, 2) as 'sinceDowntime'
FROM  places_drills pd,     reasons r , codes c,  downtimes_drills de
WHERE
	c.id_reason = r.id AND
    de.id_code = c.id AND
    de.id_place_drill = pd.id AND
    de.start_time < :shift_end AND
    ( de.end_time > :shift_month_start OR de.end_time is null ) AND
    c.deleted_at is null AND
    c.deleted_at is null AND
    pd.deleted_at is null AND
    de.deleted_at is null AND
    pd.id = :id_place_drill AND
    r.deleted_at IS NULL AND
    r.drill = 1
ORDER BY de.start_time ASC", $arrBind);
    }

    public static function downtime_by_reason($date_day, $id_place, $id_reason){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 2)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $shift_end_date = new Carbon($date_day);
        $shift_month_start_date = new Carbon($date_day); $shift_month_start_date->startOfMonth();

        $arrBind = ["shift_month_start" => $shift_month_start, "shift_end" => $shift_end, "shift_month_start_date" => $shift_month_start_date,
            "shift_end_date" => $shift_end_date, "id_place" => $id_place, "id_reason" => $id_reason];
        return DB::select("SELECT
	p.title as 'title_place',
	pd.id_drill,
    e.title as 'title_drill',
    pd.garage,
    tg.id as 'id_technica_group',
    tg.name as 'title_technica_type',
    r.type,
    r.reason,
    c.id as 'id_code',
   	c.code,
    c.title as 'title_code',
  	de.start_time,
    de.end_time,
    ROUND(TIMESTAMPDIFF(SECOND,
                  	(CASE
                  		WHEN de.start_time <= :shift_month_start
                  			THEN :shift_month_start
                  		ELSE de.start_time
                  	END),
                    (CASE
                     	WHEN de.end_time >= :shift_end OR de.end_time IS NULL
                     		THEN :shift_end
                     	ELSE de.end_time
                     END)
                  )/3600, 2) as 'sinceDuration'


FROM places p, drills e, reasons r, technica_groups tg,  places_drills pd, codes c, downtimes_drills de
WHERE
		p.id = pd.id_place AND
		c.id_reason = r.id AND
		e.id = pd.id_drill AND
        de.id_place_drill = pd.id AND
        de.id_code = c.id AND
        pd.date_from <= DATE(:shift_end_date) AND
        ( pd.date_to >= DATE(:shift_month_start_date) OR pd.date_to IS NULL ) AND
        de.start_time <= :shift_end AND
        ( de.end_time >= :shift_month_start OR de.end_time is null ) AND
      	pd.id_place = :id_place AND
        c.id_reason = :id_reason AND
        r.drill = 1 AND
        tg.id = e.id_group AND
        e.deleted_at IS NULL AND
        pd.deleted_at IS NULL AND
        c.deleted_at IS NULL AND
        r.deleted_at IS NULL AND
        de.deleted_at IS NULL
 ORDER BY de.start_time", $arrBind);
    }
}



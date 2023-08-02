<?php

namespace App\Kio\Locomotives\Downtimes;

use App\Kio\Locomotives\LocomotiveModel;
use App\Kio\Locomotives\LocomotivePlaceModel;
use App\Kio\Places\Shifts\PlaceShiftModel;
use App\Kio\Reasons\CodeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LocomotiveDowntimeModel extends Model
{
    use SoftDeletes;
    protected $table = 'downtimes_locomotives';
    protected $guarded = ['id'];
    protected $hidden = ['creator_id', 'created_at', 'updater_id', 'updated_at', 'deleter_id', 'deleted_at'];

    public function locomotive(){
        return $this->belongsTo(LocomotiveModel::class, 'id_locomotive', 'id');
    }

    public function place(){
        return $this->belongsTo(PlaceModel::class, 'id_place', 'id');
    }

    public function code(){
        return $this->belongsTo(CodeModel::class, 'id_code', 'id');
    }

    public function place_locomotive(){
        return $this->belongsTo(LocomotivePlaceModel::class, 'id_place_locomotive', 'id');
    }

    public static function downtime_plan_fact($date_day, $id_place){
        return DB::select("SELECT
            pe.id as 'id_place_locomotive',
            pe.id_place,
            pe.id_locomotive,
            e.title,
            e.serial_number,
            f.id as 'id_fact',
            p.id as 'id_plan',
            p.plan,
            f.fact,
            p.plan_month,
            f.fact_month,
            p.plan_year,
            f.fact_year
        FROM locomotives e, places_locomotives pe
                LEFT JOIN plans_locomotives p ON
                            pe.id = p.id_place_locomotive AND
                            p.date_plan = :date_day AND
                            p.deleted_at is null
                LEFT JOIN facts_locomotives f ON
                            pe.id = f.id_place_locomotive AND
                            f.date_fact = :date_day AND
                            f.deleted_at is null
        WHERE
                    pe.id_locomotive = e.id
		AND pe.id_place = :id_place
        AND pe.deleted_at is null
        AND e.deleted_at is null AND
        DATE(:date_day) BETWEEN pe.date_from AND COALESCE(pe.date_to, DATE(:date_day))", ["date_day" => $date_day, "id_place" => $id_place]);
    }

    public static function downtime_daily_by_plan_fact($date_day, $id_place, $id_place_locomotives){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_start = new Carbon($date_day); $shift_start->addMinutes($start_difference);
        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrPos = [];
        $arrBind = ["shift_start" => $shift_start, "shift_end"=> $shift_end, "shift_month_start"=> $shift_month_start];
        foreach ($id_place_locomotives as $pos => $id) {
            $arrPos[] = ":id_{$pos}";
            $arrBind["id_{$pos}"] = $id;
        }
        if(!empty($arrPos)){
            $query_part = " de.id_place_locomotive IN (".implode(', ', $arrPos).")";
        }else{
            $query_part = " de.id_place_locomotive IN ( null )";
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
                                                    WHEN :shift_start <= de.end_time  AND de.end_time <= :shift_end
                                                        THEN de.end_time
                                                    WHEN de.start_time < :shift_end AND ( :shift_end <= de.end_time OR de.end_time is NULL)
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
                                                    WHEN  :shift_month_start <= de.end_time  AND de.end_time <= :shift_end
                                                        THEN de.end_time
                                                    WHEN de.start_time <= :shift_month_start AND (:shift_end < de.end_time OR de.end_time is null)
                                                        THEN :shift_end
                                                    ELSE NULL
                                                END)
                                                )
                                )/3600, 2) as 'sinceDowntime',
                                de.id_place_locomotive,
                                de.id_locomotive

                            FROM reasons r
                                    LEFT JOIN  codes c  ON
                                        c.id_reason = r.id AND
                                        c.deleted_at is null
                                    LEFT JOIN downtimes_locomotives de   ON
                                        de.id_code = c.id  AND
                                        de.start_time <= :shift_end AND
                                        (de.end_time >= :shift_month_start OR de.end_time is null) AND
                                        ".$query_part." AND
                                        de.deleted_at is null
                            WHERE r.deleted_at IS NULL AND r.locomotive = 1
                            GROUP BY r.id, r.reason, de.id_place_locomotive, de.id_locomotive,   r.type,  r.is_reported", $arrBind);
    }

    public static function downtime_daily_by_plan_fact_by_report($date_day, $id_report, $id_place, $id_place_locomotives){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_start = new Carbon($date_day); $shift_start->addMinutes($start_difference);
        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrPos = [];
        $arrBind = ["id_report" =>$id_report, "shift_start" => $shift_start, "shift_end"=> $shift_end, "shift_month_start"=> $shift_month_start];
        foreach ($id_place_locomotives as $pos => $id) {
            $arrPos[] = ":id_{$pos}";
            $arrBind["id_{$pos}"] = $id;
        }
        if(!empty($arrPos)){
            $query_part = " de.id_place_locomotive IN (".implode(', ', $arrPos).")";
        }else{
            $query_part = " de.id_place_locomotive IN ( null )";
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
                                                    WHEN :shift_start <= de.end_time  AND de.end_time <= :shift_end
                                                        THEN de.end_time
                                                    WHEN de.start_time < :shift_end AND ( :shift_end <= de.end_time OR de.end_time is NULL)
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
                                                    WHEN  :shift_month_start <= de.end_time  AND de.end_time <= :shift_end
                                                        THEN de.end_time
                                                    WHEN de.start_time <= :shift_month_start AND (:shift_end < de.end_time OR de.end_time is null)
                                                        THEN :shift_end
                                                    ELSE NULL
                                                END)
                                                )
                                )/3600, 2) as 'sinceDowntime',
                                de.id_place_locomotive,
                                de.id_locomotive

                            FROM reasons r, reports_downtimes_codes rdc, codes c
                                    LEFT JOIN downtimes_locomotives de   ON
                                        de.id_code = c.id  AND
                                        de.start_time <= :shift_end AND
                                        (de.end_time >= :shift_month_start OR de.end_time is null) AND
                                        ".$query_part." AND
                                        de.deleted_at is null
                            WHERE   r.deleted_at IS NULL AND
                                    r.locomotive = 1 AND
                                    c.id_reason = r.id AND
                                    c.deleted_at IS NULL AND
                                    rdc.id_code = c.id AND
                                    rdc.id_report = :id_report
                            GROUP BY r.id, r.reason, de.id_place_locomotive, de.id_locomotive,   r.type,  r.is_reported", $arrBind);
    }

    public static function downtime_daily($date_day, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_start = new Carbon($date_day); $shift_start->addMinutes($start_difference);
        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);

        $arrBind = ["date_day" => $date_day, "shift_start" => $shift_start, "shift_end" => $shift_end, "id_place" => $id_place];

        return DB::select("SELECT
	pe.id_locomotive,
    pe.id_place,
    p.title as 'title_place',
    e.title as 'title_locomotive',
    e.serial_number,
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
     	WHEN de.start_time < :shift_start AND (:shift_end < de.end_time OR de.end_time IS NULL)
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
     	WHEN de.start_time < :shift_start AND ( :shift_end < de.end_time OR de.end_time IS NULL)
     		THEN ROUND(TIMESTAMPDIFF(SECOND, de.start_time, :shift_end)/3600, 2)
     	ELSE NULL
    END)  as 'sinceDuration'
FROM  locomotives e, places p, places_locomotives pe, downtimes_locomotives de, codes c, reasons r
WHERE
	pe.id_locomotive = de.id_locomotive AND
    pe.id_place = de.id_place AND
    de.id_code = c.id AND
    c.id_reason = r.id AND
    pe.id_place = p.id AND pe.id_locomotive = e.id AND
    pe.date_from <= DATE(:date_day) AND
    ( pe.date_to >= DATE(:date_day)  OR pe.date_to IS NULL ) AND
    pe.id_place = :id_place AND
    de.start_time < :shift_end AND
    ( de.end_time > :shift_start OR de.end_time is null ) AND
    de.deleted_at is null  AND
    e.deleted_at is null AND
    p.deleted_at is null AND
    pe.deleted_at is null AND
    c.deleted_at is null AND
    r.deleted_at is null AND
    r.locomotive = 1
ORDER BY de.start_time", $arrBind);
    }

    public static function downtime_exluded($date_day, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
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
                       	WHEN :shift_month_start <= de.start_time AND de.start_time <= :shift_end
                       		THEN de.start_time
                      	WHEN de.start_time <= :shift_month_start AND (:shift_month_start <= de.end_time OR de.end_time is null )
                            THEN :shift_month_start
                        ELSE NULL
                       END),
                      (CASE
                       	WHEN  :shift_month_start <= de.end_time  AND de.end_time <= :shift_end
                       		THEN de.end_time
                      	WHEN de.start_time < :shift_end AND (:shift_end <= de.end_time OR de.end_time is null)
                       		THEN :shift_end
                        ELSE NULL
                       END)
                     )
    )/3600, 2) as 'excludedDowntime'

FROM reasons r
		LEFT JOIN  codes c  ON
            c.id_reason = r.id AND
            c.deleted_at is null
		LEFT JOIN downtimes_locomotives de   ON
        	de.id_code = c.id  AND
        	de.start_time <= :shift_end AND
        	(de.end_time >= :shift_month_start OR de.end_time is null) AND
            de.id_place = :id_place AND
            de.deleted_at is null
WHERE r.deleted_at IS NULL AND r.locomotive = 1
GROUP BY r.id, r.reason,  r.type,  r.is_reported", $arrBind);
    }

    public static function downtime_exluded_by_report($date_day, $id_report, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);
        $shift_month_end = new Carbon($date_day); $shift_month_end->endOfMonth()->addMinutes($start_difference);

        $arrBind = ["id_report" => $id_report, "shift_end" => $shift_end, "shift_month_start" => $shift_month_start, "id_place" => $id_place];

        return DB::select("SELECT
	r.id as 'id_reason',
    r.reason,
    r.type,
    r.is_reported,

    ROUND(SUM(
    	TIMESTAMPDIFF(SECOND,
                      (CASE
                       	WHEN :shift_month_start <= de.start_time AND de.start_time <= :shift_end
                       		THEN de.start_time
                      	WHEN de.start_time <= :shift_month_start AND (:shift_month_start <= de.end_time OR de.end_time is null )
                            THEN :shift_month_start
                        ELSE NULL
                       END),
                      (CASE
                       	WHEN  :shift_month_start <= de.end_time  AND de.end_time <= :shift_end
                       		THEN de.end_time
                      	WHEN de.start_time < :shift_end AND (:shift_end <= de.end_time OR de.end_time is null)
                       		THEN :shift_end
                        ELSE NULL
                       END)
                     )
    )/3600, 2) as 'excludedDowntime'

FROM reasons r, reports_downtimes_codes rdc, codes c
		LEFT JOIN downtimes_locomotives de ON
        	de.id_code = c.id  AND
        	de.start_time <= :shift_end AND
        	(de.end_time >= :shift_month_start OR de.end_time is null) AND
            de.id_place = :id_place AND
            de.deleted_at is null
WHERE r.deleted_at IS NULL AND
      r.locomotive = 1 AND
      c.id_reason = r.id AND
      c.deleted_at IS NULL AND
      rdc.id_code = c.id AND
      rdc.id_report = :id_report
GROUP BY r.id, r.reason,  r.type,  r.is_reported", $arrBind);
    }

    public static function downtime_exluded_hours($date_day, $id_place){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrBind = ["shift_month_start" => $shift_month_start, "shift_end" => $shift_end, "id_place" => $id_place];
        return DB::select("SELECT
            SUM(DAY(p.date_to)*24) as 'addingHours'
            FROM places_locomotives p
            WHERE p.deleted_at is null and p.id_place = :id_place AND
            :shift_month_start <= p.date_to  AND  p.date_to < :shift_end ", $arrBind);
    }

    public static function downtime_by_locomotive($date_day, $id_place_locomotive){
        $id_place = LocomotivePlaceModel::find($id_place_locomotive)->id_place;
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
        $start_difference = $shifts?->first()?->difference;
        $end_difference = $shifts?->last()?->difference + $shifts?->last()?->duration? : 1440 + $shifts?->first()?->difference;

        $shift_end = new Carbon($date_day); $shift_end->addMinutes($end_difference);
        $shift_month_start = new Carbon($date_day); $shift_month_start->startOfMonth()->addMinutes($start_difference);

        $arrBind = ["shift_month_start" => $shift_month_start, "shift_end" => $shift_end, "id_place_locomotive" => $id_place_locomotive];
        return DB::select("SELECT
pe.id as 'id_place_locomotive',
pe.id_place as 'id_place',
pe.id_locomotive as 'id_locomotive',
r.reason,
r.type,
r.id as 'id_reason',
c.id as 'id_code',
c.code as 'code',
c.title as 'title_code',
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
                   		WHEN de.end_time > :shift_end OR de.end_time is null
                   			THEN :shift_end
                  		ELSE de.end_time
                   END
               )
              )/3600, 2) as 'sinceDowntime'
FROM  places_locomotives pe,   reasons r , codes c,  downtimes_locomotives de
WHERE
	c.id_reason = r.id AND
    de.id_code = c.id AND
    de.id_place_locomotive = pe.id AND
    de.start_time < :shift_end AND
    ( de.end_time > :shift_month_start OR de.end_time is null ) AND
    c.deleted_at is null AND
    c.deleted_at is null AND
    pe.deleted_at is null AND
    de.deleted_at is null AND
    pe.id = :id_place_locomotive  AND
    r.deleted_at IS NULL AND
    r.locomotive = 1
ORDER BY de.start_time ASC", $arrBind);
    }

    public static function downtime_by_reason($date_day, $id_place, $id_reason){
        $shifts = PlaceShiftModel::where('id_place', '=', $id_place)->where('id_technica_type', '=', 6)->orderBy('shift')->get();
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
	pe.id_locomotive,
    e.title as 'title_locomotive',
    e.serial_number,
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


FROM places p, locomotives e, reasons r, technica_groups tg,  places_locomotives pe, codes c, downtimes_locomotives de
WHERE
		p.id = pe.id_place AND
		c.id_reason = r.id AND
		e.id = pe.id_locomotive AND
        de.id_place_locomotive = pe.id AND
        de.id_code = c.id AND
        pe.date_from <= DATE(:shift_end_date) AND
        ( pe.date_to >= DATE(:shift_month_start_date) OR pe.date_to IS NULL ) AND
        de.start_time <= :shift_end AND
        ( de.end_time >= :shift_month_start OR de.end_time is null ) AND
      	pe.id_place = :id_place AND
        c.id_reason = :id_reason AND
        r.locomotive = 1 AND
        tg.id = e.id_group AND
        e.deleted_at IS NULL AND
        pe.deleted_at IS NULL AND
        c.deleted_at IS NULL AND
        r.deleted_at IS NULL AND
        de.deleted_at IS NULL
 ORDER BY de.start_time", $arrBind);
    }
}



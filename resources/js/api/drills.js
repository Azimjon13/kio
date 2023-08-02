import request from '@/utils/request';

export function storeDrill(data)
{
    return request({
        url:"/drills", 
        method: 'POST',
        data
    });
};
export function updateDrill(id, data)
{
    return request({
        url:"/drills/"+id,
        method:"PATCH", 
        data
    })
}

export function getDrill(id) 
{
    return request({
        url:"/drills/"+id,
        method:"GET",
    });
};

export function deleteDrill(id) 
{
    return request({
        url:"/drills/"+id,
        method:"DELETE",
    });
};

export function getAllDrills(query)
{
    return request({
        url:"/drills",
        method: "GET",
        params: query
    });
};

export function deleteDrillPlace(id) 
{
    return request({
        url:"/drillplaces/"+id, 
        method:"DELETE"
    });
};

export function getDrillGroups() 
{
    return request({
        url:"/technicagrouplist/2", 
        method: "GET",
    });
};

export function getDrillDowntimesDailiy(date, id_place) {
    return request({
        url:"/drilldowntimesdaily",
        method: "GET",
        params: {date, id_place}
    })
}

export function searchDrill(query) { 
    return request({ 
        url:"/drillsbygroup",
        method: "GET",
        params: query
    });
};
/**
 * 
 * @param {{date: String, id_place_drill: Number}} query 
 * @returns 
 */
export function drillDowntimes(query) 
{
    return request({
        url:"/drilldowntimesbyone", 
        method:"GET",
        params: query
    })
};


/**
 * 
 * @param {{ id_place:Number, id_reason: Number,  date: String }} query 
 * @returns {{
*                  errors: null,
*                  message:String,
*                  status: Boolean,
*                  data: {
*                      downtimes: {
*                           id_code: Number, 
*                           id_drill: Number, 
*                           id_technica_group: Number, 
*                           code: String | Number, 
*                           title_code: String, 
*                           reason: String,
*                           type: Number,  
*                           title_drill: String, 
*                           garage: String, 
*                           title_place: String, 
*                           title_technica_type: String,
*                           end_time: String | null, 
*                           start_time: String,
*                           sinceDuration: String | Number
*                        }[],
*                      shifts: {
*                          shift_end: String, 
*                          shift_month_start: String,
*                       }
*                  } 
* }}
*/
export function downtimesByReasonPlace(query) 
{
   return request({
       url:"drilldowntimesbyreason",
       method:"GET",
       params: query
   });
}

export function addDowntime(data) 
{
    return request({
        url:"/drilldowntimes",
        method: "POST",
        data
    });
}

export function updateDowntime(id, data) 
{
    return request({
        url:"/drilldowntimes/"+id, 
        method:"PATCH", 
        data 
    });
}
export function deleteDowntime(id) 
{
    return request({
        url:"/drilldowntimes/"+id, 
        method:"DELETE",
    });
}

export function getDowntime(id) 
{
    return request({
        url:"/drilldowntimes/"+id, 
        method: "GET",
    });
}

export function getMainData(date, id_place) 
{   
    return request({
        url:"/drillplansfactsdowntimes",
        method: "GET",
        params: {date, id_place}
    })
}

export function storePlan(data) 
{
    return request({
        url:'/drillplans',
        method:"POST",
        data
    });
}

export function storeFact(data) 
{
    return request({
        url:'/drillfacts',
        method:"POST",
        data
    });
}

export function drillDatasByReport(id_report, date) 
{
    return request({
        url:"/drillplansfactsdowntimesbyreport",
        method:"GET",
        params: {id_report, date}
    });
}
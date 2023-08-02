import request from '@/utils/request';

export function storeDsm(data)
{
    return request({
        url:"/dsms", 
        method: 'POST',
        data
    });
};
export function updateDsm(id, data)
{
    return request({
        url:"/dsms/"+id,
        method:"PATCH", 
        data
    })
}

export function getDsm(id) 
{
    return request({
        url:"/dsms/"+id,
        method:"GET",
    });
};

export function deleteDsm(id) 
{
    return request({
        url:"/dsms/"+id,
        method:"DELETE",
    });
};

export function getAllDsms(query)
{
    return request({
        url:"/dsms",
        method: "GET",
        params: query
    });
};

export function deleteDsmPlace(id) 
{
    return request({
        url:"/dsmplaces/"+id, 
        method:"DELETE"
    });
};

export function getDsmGroups() 
{
    return request({
        url:"/technicagrouplist/5", 
        method: "GET",
    });
};

export function getDsmDowntimesDailiy(date, id_place) {
    return request({
        url:"/dsmdowntimesdaily",
        method: "GET",
        params: {date, id_place}
    })
}

export function searchDsm(query) { 
    return request({ 
        url:"/dsmsbygroup",
        method: "GET",
        params: query
    });
};
/**
 * 
 * @param {{date: String, id_place_dsm: Number}} query 
 * @returns 
 */
export function dsmDowntimes(query) 
{
    return request({
        url:"/dsmdowntimesbyone", 
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
*                           id_dsm: Number, 
*                           id_technica_group: Number, 
*                           code: String | Number, 
*                           title_code: String, 
*                           reason: String,
*                           type: Number,  
*                           title_dsm: String, 
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
       url:"dsmdowntimesbyreason",
       method:"GET",
       params: query
   });
}

export function addDowntime(data) 
{
    return request({
        url:"/dsmdowntimes",
        method: "POST",
        data
    });
}

export function updateDowntime(id, data) 
{
    return request({
        url:"/dsmdowntimes/"+id, 
        method:"PATCH", 
        data 
    });
}
export function deleteDowntime(id) 
{
    return request({
        url:"/dsmdowntimes/"+id, 
        method:"DELETE",
    });
}

export function getDowntime(id) 
{
    return request({
        url:"/dsmdowntimes/"+id, 
        method: "GET",
    });
}

export function getMainData(date, id_place) 
{   
    return request({
        url:"/dsmplansfactsdowntimes",
        method: "GET",
        params: {date, id_place}
    })
}

export function storePlan(data) 
{
    return request({
        url:'/dsmplans',
        method:"POST",
        data
    });
}

export function storeFact(data) 
{
    return request({
        url:'/dsmfacts',
        method:"POST",
        data
    });
}

export function dsmDatasByReport(id_report, date) 
{
    return request({
        url:"/dsmplansfactsdowntimesbyreport",
        method:"GET",
        params: {id_report, date}
    });
}
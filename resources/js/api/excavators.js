import request from '@/utils/request';

export function storeExcavator(data)
{
    return request({
        url:"/excavators", 
        method: 'POST',
        data
    });
};
export function updateExcavator(id, data)
{
    return request({
        url:"/excavators/"+id,
        method:"PATCH", 
        data
    })
}

export function getExcavator(id) 
{
    return request({
        url:"/excavators/"+id,
        method:"GET",
    });
};

export function deleteExcavator(id) 
{
    return request({
        url:"/excavators/"+id,
        method:"DELETE",
    });
};

export function getAllExcavators(query)
{
    return request({
        url:"/excavators",
        method: "GET",
        params: query
    });
};

export function deleteExcavatorPlace(id) 
{
    return request({
        url:"/excavatorplaces/"+id, 
        method:"DELETE"
    });
};

export function getExcavatorGroups() 
{
    return request({
        url:"/technicagrouplist/1", 
        method: "GET",
    });
};

export function getExcavatorDowntimesDailiy(date, id_place) {
    return request({
        url:"/excavatordowntimesdaily",
        method: "GET",
        params: {date, id_place}
    })
}

export function searchExcavator(query) { 
    return request({ 
        url:"/excavatorsbygroup",
        method: "GET",
        params: query
    });
};
/**
 * 
 * @param {{date: String, id_place_excavator: Number}} query 
 * @returns 
 */
export function excavatorDowntimes(query) 
{
    return request({
        url:"/excavatordowntimesbyone", 
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
*                           id_excavator: Number, 
*                           id_technica_group: Number, 
*                           code: String | Number, 
*                           title_code: String, 
*                           reason: String,
*                           type: Number,  
*                           title_excavator: String, 
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
        url:"excavatordowntimesbyreason",
        method:"GET",
        params: query
    });
}

export function addDowntime(data) 
{
    return request({
        url:"/excavatordowntimes",
        method: "POST",
        data
    });
}

export function updateDowntime(id, data) 
{
    return request({
        url:"/excavatordowntimes/"+id, 
        method:"PATCH", 
        data 
    });
}
export function deleteDowntime(id) 
{
    return request({
        url:"/excavatordowntimes/"+id, 
        method:"DELETE",
    });
}

export function getDowntime(id) 
{
    return request({
        url:"/excavatordowntimes/"+id, 
        method: "GET",
    });
}

export function getMainData(date, id_place) 
{   
    return request({
        url:"/excavatorplansfactsdowntimes",
        method: "GET",
        params: {date, id_place}
    })
}

export function storePlan(data) 
{
    return request({
        url:'/excavatorplans',
        method:"POST",
        data
    });
}

export function storeFact(data) 
{
    return request({
        url:'/excavatorfacts',
        method:"POST",
        data
    });
}

export function excavatorDatasByReport(id_report, date) 
{
    return request({
        url:"/excavatorplansfactsdowntimesbyreport",
        method:"GET",
        params: {id_report, date}
    });
}
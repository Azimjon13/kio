import request from '@/utils/request';

export function storeCpt(data)
{
    return request({
        url:"/cpts", 
        method: 'POST',
        data
    });
};
export function updateCpt(id, data)
{
    return request({
        url:"/cpts/"+id,
        method:"PATCH", 
        data
    })
}

export function getCpt(id) 
{
    return request({
        url:"/cpts/"+id,
        method:"GET",
    });
};

export function deleteCpt(id) 
{
    return request({
        url:"/cpts/"+id,
        method:"DELETE",
    });
};

export function getAllCpts(query)
{
    return request({
        url:"/cpts",
        method: "GET",
        params: query
    });
};

export function deleteCptPlace(id) 
{
    return request({
        url:"/cptplaces/"+id, 
        method:"DELETE"
    });
};

export function getCptGroups() 
{
    return request({
        url:"/technicagrouplist/3", 
        method: "GET",
    });
};

export function getCptDowntimesDailiy(date, id_place) {
    return request({
        url:"/cptdowntimesdaily",
        method: "GET",
        params: {date, id_place}
    })
}

export function searchCpt(query) { 
    return request({ 
        url:"/cptsbygroup",
        method: "GET",
        params: query
    });
};
/**
 * 
 * @param {{date: String, id_place_cpt: Number}} query 
 * @returns 
 */
export function cptDowntimes(query) 
{
    return request({
        url:"/cptdowntimesbyone", 
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
*                           id_cpt: Number, 
*                           id_technica_group: Number, 
*                           code: String | Number, 
*                           title_code: String, 
*                           reason: String,
*                           type: Number,  
*                           title_cpt: String, 
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
       url:"cptdowntimesbyreason",
       method:"GET",
       params: query
   });
}

export function addDowntime(data) 
{
    return request({
        url:"/cptdowntimes",
        method: "POST",
        data
    });
}

export function updateDowntime(id, data) 
{
    return request({
        url:"/cptdowntimes/"+id, 
        method:"PATCH", 
        data 
    });
}
export function deleteDowntime(id) 
{
    return request({
        url:"/cptdowntimes/"+id, 
        method:"DELETE",
    });
}

export function getDowntime(id) 
{
    return request({
        url:"/cptdowntimes/"+id, 
        method: "GET",
    });
}

export function getMainData(date, id_place) 
{   
    return request({
        url:"/cptplansfactsdowntimes",
        method: "GET",
        params: {date, id_place}
    })
}

export function storePlan(data) 
{
    return request({
        url:'/cptplans',
        method:"POST",
        data
    });
}

export function storeFact(data) 
{
    return request({
        url:'/cptfacts',
        method:"POST",
        data
    });
}

export function cptDatasByReport(id_report, date) 
{
    return request({
        url:"/cptplansfactsdowntimesbyreport",
        method:"GET",
        params: {id_report, date}
    });
}
import request from '@/utils/request';

export function storeLocomotive(data)
{
    return request({
        url:"/locomotives", 
        method: 'POST',
        data
    });
};
export function updateLocomotive(id, data)
{
    return request({
        url:"/locomotives/"+id,
        method:"PATCH", 
        data
    })
}

export function getLocomotive(id) 
{
    return request({
        url:"/locomotives/"+id,
        method:"GET",
    });
};

export function deleteLocomotive(id) 
{
    return request({
        url:"/locomotives/"+id,
        method:"DELETE",
    });
};

export function getAllLocomotives(query)
{
    return request({
        url:"/locomotives",
        method: "GET",
        params: query
    });
};

export function deleteLocomotivePlace(id) 
{
    return request({
        url:"/locomotiveplaces/"+id, 
        method:"DELETE"
    });
};

export function getLocomotiveGroups() 
{
    return request({
        url:"/technicagrouplist/6", 
        method: "GET",
    });
};

export function getLocomotiveDowntimesDailiy(date, id_place) {
    return request({
        url:"/locomotivedowntimesdaily",
        method: "GET",
        params: {date, id_place}
    })
}

export function searchLocomotive(query) { 
    return request({ 
        url:"/locomotivesbygroup",
        method: "GET",
        params: query
    });
};
/**
 * 
 * @param {{date: String, id_place_locomotive: Number}} query 
 * @returns 
 */
export function locomotiveDowntimes(query) 
{
    return request({
        url:"/locomotivedowntimesbyone", 
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
*                           id_locomotive: Number, 
*                           id_technica_group: Number, 
*                           code: String | Number, 
*                           title_code: String, 
*                           reason: String,
*                           type: Number,  
*                           title_locomotive: String, 
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
       url:"locomotivedowntimesbyreason",
       method:"GET",
       params: query
   });
}

export function addDowntime(data) 
{
    return request({
        url:"/locomotivedowntimes",
        method: "POST",
        data
    });
}

export function updateDowntime(id, data) 
{
    return request({
        url:"/locomotivedowntimes/"+id, 
        method:"PATCH", 
        data 
    });
}
export function deleteDowntime(id) 
{
    return request({
        url:"/locomotivedowntimes/"+id, 
        method:"DELETE",
    });
}

export function getDowntime(id) 
{
    return request({
        url:"/locomotivedowntimes/"+id, 
        method: "GET",
    });
}

export function getMainData(date, id_place) 
{   
    return request({
        url:"/locomotiveplansfactsdowntimes",
        method: "GET",
        params: {date, id_place}
    })
}

export function storePlan(data) 
{
    return request({
        url:'/locomotiveplans',
        method:"POST",
        data
    });
}

export function storeFact(data) 
{
    return request({
        url:'/locomotivefacts',
        method:"POST",
        data
    });
}

export function locomotiveDatasByReport(id_report, date) 
{
    return request({
        url:"/locomotiveplansfactsdowntimesbyreport",
        method:"GET",
        params: {id_report, date}
    });
}
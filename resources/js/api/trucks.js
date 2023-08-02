import request from '@/utils/request';

export function storeTruck(data)
{
    return request({
        url:"/trucks", 
        method: 'POST',
        data
    });
};
export function updateTruck(id, data)
{
    return request({
        url:"/trucks/"+id,
        method:"PATCH", 
        data
    })
}

export function getTruck(id) 
{
    return request({
        url:"/trucks/"+id,
        method:"GET",
    });
};

export function deleteTruck(id) 
{
    return request({
        url:"/trucks/"+id,
        method:"DELETE",
    });
};

export function getAllTrucks(query)
{
    return request({
        url:"/trucks",
        method: "GET",
        params: query
    });
};

export function deleteTruckPlace(id) 
{
    return request({
        url:"/truckplaces/"+id, 
        method:"DELETE"
    });
};

export function getTruckGroups() 
{
    return request({
        url:"/technicagrouplist/4", 
        method: "GET",
    });
};

export function getTruckDowntimesDailiy(date, id_place) {
    return request({
        url:"/truckdowntimesdaily",
        method: "GET",
        params: {date, id_place}
    })
}

export function searchTruck(query) { 
    return request({ 
        url:"/trucksbygroup",
        method: "GET",
        params: query
    });
};
/**
 * 
 * @param {{date: String, id_place_truck: Number}} query 
 * @returns 
 */
export function truckDowntimes(query) 
{
    return request({
        url:"/truckdowntimesbyone", 
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
*                           id_truck: Number, 
*                           id_technica_group: Number, 
*                           code: String | Number, 
*                           title_code: String, 
*                           reason: String,
*                           type: Number,  
*                           title_truck: String, 
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
       url:"truckdowntimesbyreason",
       method:"GET",
       params: query
   });
}

export function addDowntime(data) 
{
    return request({
        url:"/truckdowntimes",
        method: "POST",
        data
    });
}

export function updateDowntime(id, data) 
{
    return request({
        url:"/truckdowntimes/"+id, 
        method:"PATCH", 
        data 
    });
}
export function deleteDowntime(id) 
{
    return request({
        url:"/truckdowntimes/"+id, 
        method:"DELETE",
    });
}

export function getDowntime(id) 
{
    return request({
        url:"/truckdowntimes/"+id, 
        method: "GET",
    });
}

export function getMainData(date, id_place) 
{   
    return request({
        url:"/truckplansfactsdowntimes",
        method: "GET",
        params: {date, id_place}
    })
}

export function storePlan(data) 
{
    return request({
        url:'/truckplans',
        method:"POST",
        data
    });
}

export function storeFact(data) 
{
    return request({
        url:'/truckfacts',
        method:"POST",
        data
    });
}

export function truckDatasByReport(id_report, date) 
{
    return request({
        url:"/truckplansfactsdowntimesbyreport",
        method:"GET",
        params: {id_report, date}
    });
}
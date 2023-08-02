import request from '@/utils/request';

export function storeReason(data)
{
    return request({
        url:"/reasons", 
        method: 'POST',
        data
    });
};
export function updateReason(id, data)
{
    return request({
        url:"/reasons/"+id,
        method:"PATCH", 
        data
    })
}


export function getReason(id) 
{
    return request({
        url:"/reasons/"+id,
        method:"GET",
    });
};


export function getReasonsSelect(query)
{
    return request({
        url:"/reasonsselect", 
        method: "GET",
        params: query
    });
};

export function deleteReason(id) 
{
    return request({
        url:"/reasons/"+id,
        method:"DELETE",
    });
};
export function reasonsByTechnicaTypeId(id)
{
    return request({
        url:"/reasonsselectbytechnicatype/"+id,
        method:"GET", 
    })
}


export function getAllReasons(query)
{
    return request({
        url:"/reasons",
        method: "GET",
        params: query
    });
};



export function reasonsWithCodes(aggregate) 
{
    return request({
        url:"/reasonswithcodes"+aggregate,
        method:"GET"
    })
}

import request from '@/utils/request';

export function storeCode(data)
{
    return request({
        url:"/codes", 
        method: 'POST',
        data
    });
};
export function updateCode(id, data)
{
    return request({
        url:"/codes/"+id,
        method:"PATCH", 
        data
    })
}


export function getCode(id) 
{
    return request({
        url:"/codes/"+id,
        method:"GET",
    });
};

export function deleteCode(id) 
{
    return request({
        url:"/codes/"+id,
        method:"DELETE",
    });
};


export function getAllCodes(query)
{
    return request({
        url:"/codes",
        method: "GET",
        params: query
    });
};


export function searchCode(query) 
{
    return request({
        url:"/codesselect",
        method: "GET",
        params: query
    });
};


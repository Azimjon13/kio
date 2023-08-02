import request from '@/utils/request';


export function getManager(id, aggregate = '')
{
    return request({
        url:"/placesbymanager/"+id+aggregate,
        method:"GET"
    });
};

export function fetchPlaces()
{
    return request({
        url:"/managers", 
        method: 'get',
    });
};

export function getPlaceForExcel(id)
{
    return request({
        url:"/placebyid/"+id,
        method:"get",
    });
}

export function storePlace(data) 
{
    return request({
        url:"/places", 
        method:"POST", 
        data
    });
};

export function updatePlace(id, data)
{
    return request({
        url:"/places/"+id,
        method: "PATCH",
        data
    });
};

export function deletePlace(id)
{
    return request({
        url:"/places/"+id,
        method:"DELETE",
    });
};



import request from '@/utils/request';

/**
 * 
 * @returns {{errors: Null | [], message: String, status: Boolean, data: {id: Number, title: String, aggregate:String}[]}}
 */
export function technicaTypes()
{
    return request({
        url:"/technicatypes",
        method:"GET"
    });
};

export function technicaGroups()
{
    return request({
        url:"/technicagrouplist",
        method:"GET"
    });
};
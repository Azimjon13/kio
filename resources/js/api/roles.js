import request from '@/utils/request'; 

export function fetchRoles() {
    return request({
        url:"/roleslist",
        method:"GET"
    });
}; 
import request from '@/utils/request';



/**
 * Сохранение отчёта
 * @param {{description:String, id_place: Number, id_technica_type:Number, codes: Number[]}} data 
 * @returns {{ data: {description: String, id: Number, id_place: Number, id_technica_type: Number} | null, errors:null | [], message:String, status: Boolean }} 
 */
export function storeReport(data)
{
    return request({
        url:"/reports",
        method:"POST",
        data
    })
}

/**
 * Получение одного отчёта
 * @param {Number} id 
 * @returns {{ errors: null | [], message:String, status: Boolean, data: {description: String, id: Number, id_manager: Number, id_parent: Number, id_place: Number, id_technica_type:Number, place:String, technica_type:String, downtimes_codes: Number[]} }}
 */
export function getReport(id)
{
    return request({
        url:"/reports/"+id,
        method:"GET"
    });
}


/**
 * Удаление отчёта
 * @param {Number} id 
 * @returns {{ data: {} | null | String, errors:null | [], message:String, status: Boolean }} 
 */
export function deleteReport(id)
{
    return request({
        url:"/reports/"+id,
        method:"DELETE"
    });
}

/**
 * Обновление отчёта
 * @param {Number} id 
 * @param {{id: Number, description:String, id_place: Number, id_technica_type:Number, codes: Number[]}} data 
 * @returns {{ data: {} | null | String, errors:null | [], message:String, status: Boolean }} 
 */
export function updateReport(id, data)
{
    return request({
        url:"/reports/"+id,
        method:"PATCH",
        data
    });
}



/**
 * Получение отчётов с пагинацией и с фильтром по tecnica_type и id_place 
 * @param {{
 *      search: String | null, 
 *      page: Number, 
 *      per_page: Number, 
 *      total: 0,
 *      sort: String,
 *      id_technica_type: Number | null,
 *      id_place: Number | null
 * }} query 
 * @returns {{
 *      errors: null | [], 
 *      message: String, 
 *      status: Boolean, 
 *      pagination: {
 *              current_page: Number, 
 *              per_page: Number, 
 *              data: {id: Number, id_place: Number, id_technica_type: Number, description: String, id_manager: Number, id_parent: Number, place: String, technica_type: String}[],
 *              from: Number, 
 *              to: Number,
 *              total: Number,
 *              path: String,
 *              link: {url:String, active: Boolean, label: String}[],
 *              prev_page_url:String | Null,
 *              last_page_url:String | Null,
 *              last_page: Number,
 *              first_page_url:String | Null,
 *          }}}
 */
export function getReports(query)
{
    return request({
        url:"/reports",
        method:"GET",
        params: query
    })
}
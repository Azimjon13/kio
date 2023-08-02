/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const dsmRoutes = {
    path:"/dsms",
    component: Layout,
    alwaysShow: true,
    meta:{
        title:"ДСМ",
        icon:"road-roller-1",
        // permissions: ['view menu excel'],
        permissions: ['view menu dsms'],
    }, 
    children: [
        {
            path:"/dsms/cru",
            component: () => import('@/views/dsms/index'),
            name:"dsmCru",
            meta: {
                    title:"ЦРУ", 
                    icon:'tree', 
                    managerId: 1,
                    permissions: ['view menu cru'],
                }
        },
        {
            path:"/dsms/sevru",
            component: () => import('@/views/dsms/index'),
            name:"dsmSevru",
            meta: {
                    title:"СевРУ", 
                    icon:'tree', 
                    managerId: 2,
                    permissions: ['view menu sevru'],
                }
        },
        {
            path:"/dsms/yuru",
            component: () => import('@/views/dsms/index'),
            name:"dsmYuru",
            meta: {
                    title:"ЮРУ", 
                    icon:'tree', 
                    managerId: 3,
                    permissions: ['view menu yuru'],
                }
        },
        {
            path:"/dsms/gmz1",
            component: () => import('@/views/dsms/index'),
            name:"dsmgmz1",
            meta: {
                    title:'РУ "ГМЗ-1"', 
                    icon:'tree', 
                    managerId: 4,
                    permissions: ['view menu gmz1'],
                }
        },
        {
            path:"/dsms/dsmsReport/:id_report",
            component: () => import('@/views/dsms/DsmsReport'),
            name:"DsmsReport",
            hidden: true, 
            meta: {
                title:'Отчёт по ДСМ', 
            }
        }
    ],



}


export default dsmRoutes;
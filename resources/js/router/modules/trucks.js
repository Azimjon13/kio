/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const truckRoutes = {
    path:"/trucks",
    component: Layout,
    alwaysShow: true,
    meta:{
        title:"Автосамосвалы",
        icon:"truck_4",
        // permissions: ['view menu excel'],
        permissions: ['view menu trucks'],
    }, 
    children: [
        {
            path:"/trucks/cru",
            component: () => import('@/views/trucks/index'),
            name:"truckCru",
            meta: {
                    title:"ЦРУ", 
                    icon:'tree', 
                    managerId: 1,
                    permissions: ['view menu cru'],
                }
        },
        {
            path:"/trucks/sevru",
            component: () => import('@/views/trucks/index'),
            name:"truckSevru",
            meta: {
                    title:"СевРУ", 
                    icon:'tree', 
                    managerId: 2,
                    permissions: ['view menu sevru'],
                }
        },
        {
            path:"/trucks/yuru",
            component: () => import('@/views/trucks/index'),
            name:"truckYuru",
            meta: {
                    title:"ЮРУ", 
                    icon:'tree', 
                    managerId: 3,
                    permissions: ['view menu yuru'],
                }
        },
        {
            path:"/trucks/gmz1",
            component: () => import('@/views/trucks/index'),
            name:"truckgmz1",
            meta: {
                    title:'РУ "ГМЗ-1"', 
                    icon:'tree', 
                    managerId: 4,
                    permissions: ['view menu gmz1'],
                }
        },
        {
            path:"/trucks/trucksReport/:id_report",
            component: () => import('@/views/trucks/TrucksReport'),
            name:"TrucksReport",
            hidden: true, 
            meta: {
                title:'Отчёт по Автосамосвалам', 
            }
        }
    ],



}


export default truckRoutes;
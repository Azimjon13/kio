/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const locomotiveRoutes = {
    path:"/locomotives",
    component: Layout,
    alwaysShow: true,
    meta:{
        title:"Тепловозы",
        icon:"locomotive-4",
        // permissions: ['view menu excel'],
        permissions: ['view menu locomotives'],
    }, 
    children: [
        {
            path:"/locomotives/cru",
            component: () => import('@/views/locomotives/index'),
            name:"locomotiveCru",
            meta: {
                    title:"ЦРУ", 
                    icon:'tree', 
                    managerId: 1,
                    permissions: ['view menu cru'],
                }
        },
        {
            path:"/locomotives/sevru",
            component: () => import('@/views/locomotives/index'),
            name:"locomotiveSevru",
            meta: {
                    title:"СевРУ", 
                    icon:'tree', 
                    managerId: 2,
                    permissions: ['view menu sevru'],
                }
        },
   
        {
            path:"/locomotives/gmz1",
            component: () => import('@/views/locomotives/index'),
            name:"locomotivegmz1",
            meta: {
                    title:'РУ "ГМЗ-1"', 
                    icon:'tree', 
                    managerId: 4,
                    permissions: ['view menu gmz1'],
                }
        },
        {
            path:"/locomotives/locomotivesReport/:id_report",
            component: () => import('@/views/locomotives/LocomotivesReport'),
            name:"LocomotivesReport",
            hidden: true, 
            meta: {
                title:'Отчёт по Тепловозам', 
            }
        }
    ],



}


export default locomotiveRoutes;
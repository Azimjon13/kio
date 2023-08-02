/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';


const excavatorRoutes = {
    path:"/excavators",
    component: Layout,
    alwaysShow: true,
    meta:{
        title:"Экскаваторы",
        icon:"excavator-second",
        permissions: ['view menu excavators'],
    }, 
    children: [
        {
            path:"/excavators/cru",
            component: () => import('@/views/excavators/index'),
            name:"excavatorCru",
            meta: {
                    title:"ЦРУ", 
                    icon:'tree', 
                    managerId: 1,
                    permissions: ['view menu cru'],
                }
        },
        {
            path:"/excavators/sevru",
            component: () => import('@/views/excavators/index'),
            name:"excavatorSevru",
            meta: {
                    title:"СевРУ", 
                    icon:'tree', 
                    managerId: 2,
                    permissions: ['view menu sevru'],
                }
        },
        {
            path:"/excavators/yuru",
            component: () => import('@/views/excavators/index'),
            name:"excavatorYuru",
            meta: {
                    title:"ЮРУ", 
                    icon:'tree', 
                    managerId: 3,
                    permissions: ['view menu yuru'],
                }
        },
        {
            path:"/excavators/gmz1",
            component: () => import('@/views/excavators/index'),
            name:"excavatorgmz1",
            meta: {
                    title:'РУ "ГМЗ-1"', 
                    icon:'tree', 
                    managerId: 4,
                    permissions: ['view menu gmz1'],
                }
        },
        {
            path:"/excavators/excavatorsReport/:id_report",
            component: () => import('@/views/excavators/ExcavatorsReport'),
            name:"ExcavatorsReport",
            hidden: true,
            meta: {
                title:'Отчёт по экскаваторам', 
        
            }
        }

    ],



}


export default excavatorRoutes;
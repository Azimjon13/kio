/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';


const drillRoutes = {
    path:"/drills",
    component: Layout,
    alwaysShow: true,
    meta:{
        title:"Буровые станки",
        icon:"drill",
        permissions: ['view menu drills'],
        
        // permissions: ['view menu drills'],
    }, 
    children: [
        {
            path:"/drills/cru",
            component: () => import('@/views/drills/index'),
            name:"drillCru",
            meta: {
                    title:"ЦРУ", 
                    icon:'tree', 
                    managerId: 1,
                    permissions: ['view menu cru'],
                }
        },
        {
            path:"/drills/sevru",
            component: () => import('@/views/drills/index'),
            name:"drillSevru",
            meta: {
                    title:"СевРУ", 
                    icon:'tree', 
                    managerId: 2,
                    permissions: ['view menu sevru'],
                }
        },
        {
            path:"/drills/yuru",
            component: () => import('@/views/drills/index'),
            name:"drillYuru",
            meta: {
                    title:"ЮРУ", 
                    icon:'tree', 
                    managerId: 3,
                    permissions: ['view menu yuru'],
                }
        },
        {
            path:"/drills/gmz1",
            component: () => import('@/views/drills/index'),
            name:"drillgmz1",
            meta: {
                    title:'РУ "ГМЗ-1"', 
                    icon:'tree', 
                    managerId: 4,
                    permissions: ['view menu gmz1'],
                }
        },
        {
            path:"/drills/drillsReport/:id_report",
            component: () => import('@/views/drills/DrillsReport'),
            name:"DrillsReport",
            hidden: true, 
            meta: {
                title:'Отчёт по Буровым станкам', 
            }
        }
    ],



}


export default drillRoutes;
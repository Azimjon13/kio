/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const cptRoutes = {
    path:"/cpts",
    component: Layout,
    alwaysShow: true,
    meta:{
        title:"ЦПТ",
        icon:"conveyor-3",
        // permissions: ['view menu excel'],
        permissions: ['view menu cpts'],
    }, 
    children: [
        {
            path:"/cpts/cru",
            component: () => import('@/views/cpts/index'),
            name:"cptCru",
            meta: {
                    title:"ЦРУ", 
                    icon:'tree', 
                    managerId: 1,
                    permissions: ['view menu cru'],
                }
        },
        {
            path:"/cpts/sevru",
            component: () => import('@/views/cpts/index'),
            name:"cptSevru",
            meta: {
                    title:"СевРУ", 
                    icon:'tree', 
                    managerId: 2,
                    permissions: ['view menu sevru'],
                }
        },
    
        {
            path:"/cpts/cptsReport/:id_report",
            component: () => import('@/views/cpts/CptsReport'),
            name:"CptsReport",
            hidden: true, 
            meta: {
                title:'Отчёт по ЦПТ', 
            }
        }
    ],



}


export default cptRoutes;
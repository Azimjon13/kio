/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';


const catalogRoutes = {
    path:"/catalog",
    component: Layout,
 
    meta:{
        title:"Справочники",
        icon:"list", 
        permissions:['view menu catalog'],
    }, 
    children: [
        {
            path:"/catalog/places", 
            component: () => import('@/views/catalogs/places/index'),
            name:"catalogPlaces",
            meta: {title:"Справочник Площадок", icon:'list',}
        },
        {
            path:"/catalog/reasons", 
            component: () => import('@/views/catalogs/reasons/index'),
            name:"catalogReasons",
            meta: {title:"Справочник Простоев", icon:'list',}
        },
        {
            path:"/catalog/excavators",
            component: () => import('@/views/catalogs/excavators/index'), 
            name: "catalogExcavators", 
            meta: {title: "Справочник Экскаваторов", icon:"list"},
        },
        {
            path:"/catalog/drills",
            component: () => import('@/views/catalogs/drills/index'), 
            name: "catalogDrills", 
            meta: {title: "Справочник Буровых станков", icon:"list"},
        },
        
        {
            path: '/catalog/trucks',
            component: () => import('@/views/catalogs/trucks/index'),
            name: 'catalogTrucks',
            meta: { title: 'Справочник Автосамосвалов', icon: 'list',},
        }, 
        {
            path: '/catalog/dsms',
            component: () => import('@/views/catalogs/dsms/index'),
            name: 'catalogDsms',
            meta: { title: 'Справочник ДСМ', icon: 'list',},
        },
        {
            path: '/catalog/locomotives',
            component: () => import('@/views/catalogs/locomotives/index'),
            name: 'catalogLocomotives',
            meta: { title: 'Справочник Тепловозов', icon: 'list',},
        },
        {
            path: '/catalog/cpts',
            component: () => import('@/views/catalogs/cpts/index'),
            name: 'catalogCpts',
            meta: { title: 'Справочник ЦПТ', icon: 'list',},
        },
        
        {
            path: '/catalog/reports',
            component: () => import('@/views/catalogs/reports/index'),
            name: 'reports',
            meta: { title: 'Справочник Отчётов', icon: 'list', permissions: ['view reports']},
        },
    ],
}


export default catalogRoutes;
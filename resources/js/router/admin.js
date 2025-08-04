import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import { authGuard } from './admin/authGuard';
import frontendRoutes from './admin/frontendRoutes';
import masterSetupRoutes from './admin/masterSetupRoutes';
import primaryResultRoutes from './admin/primaryResultRoutes';
import secondaryResultRoutes from './admin/secondaryResultRoutes';
import higherSecondaryResultRoute from './admin/higherSecondaryResultRoute';
import studentRoutes from './admin/studentRoutes';
import paymentRoutes from './admin/paymentRoutes';
import systemsRoutes from './admin/systemsRoutes';

const routes = [
    {
        path: '/dashboard', component: () => import('@views/backend/Layout'), beforeEnter: authGuard,
        children: [
            ...frontendRoutes,
            ...masterSetupRoutes,
            ...primaryResultRoutes,
            ...secondaryResultRoutes,
            ...higherSecondaryResultRoute,
            ...studentRoutes,
            ...paymentRoutes,
            ...systemsRoutes,
        ]
    },


    {
        path: '*', component: () => import('@views/backend/NotFoundLayout'), beforeEnter: authGuard,
        children: [
            { path: '*', name: '404', component: () => import('@views/errors/404'), beforeEnter: authGuard }
        ]
    }
];

const router = new VueRouter({
    routes,
    hashbang: false,
    mode: 'history',
    base: process.env.MIX_VUE_ROUTER_BASE + '/admin/',
    linkExactActiveClass: "active",
    scrollBehavior(to, from, savedPosition) {
        return {
            top: 0,
            behavior: 'instant',
        }
    }
})

export default router

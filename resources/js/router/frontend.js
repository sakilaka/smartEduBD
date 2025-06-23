import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

// ========== define route==========
const routes = [
    {
        path: '/', component: () => import('./../views/frontend/Layout'),
        // chlidren menu
        children: [
            { path: '/login', name: 'login', component: () => import('./../views/login/FrontLogin') },
            { path: '/', name: 'front.home', component: () => import('./../views/frontend/Home') },

            { /*==========USER DASHBOARD==========*/
                path: '/user',
                component: () => import('./../views/frontend/user/Layout'), beforeEnter: authGuard,
                children: [
                    { path: '/user/dashboard', name: 'user.dashboard', component: () => import('./../views/frontend/user/user/Dashboard'), beforeEnter: authGuard },
                    { path: '/user/profile', name: 'user.profile', component: () => import('./../views/frontend/user/user/Profile'), beforeEnter: authGuard },
                ]
            }
        ],
    },

]

// ========== define router==========
const router = new VueRouter({
    routes,
    hashbang: false,
    mode: 'history',
    base: process.env.MIX_VUE_ROUTER_BASE,
    linkExactActiveClass: "active",
    scrollBehavior() {
        return { x: 0, y: 0 };
    }
});


// ==========check login or not==========
function authGuard(to, from, next) {
    if (Boolean(User.loggedIn())) {
        next();
    } else {
        next({ name: 'login' });
    }
}

router.beforeEach((to, from, next) => {
    next();
    // active parent menu
    // setTimeout(() => {
    //     $('.nav-item').children('.nav-link').removeClass('parent-active')
    //     $('.router-link-exact-active').parents('.dropdown-menu').siblings('#navbarDropdown').addClass('parent-active');
    // }, 100);
})

export default router

require('./frontend_bootstrap');
window.Vue = require('vue').default;
require('./helpers/frontend/crud.js');
require('./helpers/backend/filter.js');
require('./helpers/frontend/mixin.js');
require('./helpers/frontend/plugin.js');

// ===============router=============
import router from "./router/frontend.js";

Vue.component('app', require('./components/FrontendApp.vue').default);
// ===============app===============
const app = new Vue({
    el: '#app',
    router,
    data: {
        baseurl: laravel.baseurl,
        guardian_url: laravel.guardian_url,
        spinner: false,
        userLoggedIn: false,
        userimage: `${laravel.baseurl}/images/user.png`,
        noimage: `${laravel.baseurl}/images/noimage.png`,
        attach: `${laravel.baseurl}/images/attach.png`,
        pageNumber: 1,
        menus: [],
        config: [],
        global: []
    },
    methods: {
        getMenus: function () {
            axios.get("/get-menus")
                .then(res => {
                    this.menus = res.data.menus;
                    this.config = res.data.config;
                });
        },
        globalData: function () {
            axios.get('/global-data').then(res => (this.global = res.data));
        },
    },
    mounted() {
        // this.getMenus();
        // this.globalData();
        // this.userLoggedIn = Boolean(User.loggedIn());
    },
    created() {
        // active parent menu
        // $('.nav-item').children('.nav-link').removeClass('parent-active')
        // setTimeout(() => {
        //     $('.router-link-exact-active').parents('.dropdown-menu').siblings('#navbarDropdown').addClass('parent-active');
        // }, 3000);
    }
});

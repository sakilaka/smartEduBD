require('./bootstrap');
window.Vue = require('vue').default;
require('./helpers/backend/crud');
require('./helpers/backend/mixin');
require('./helpers/backend/mixin_data');
require('./helpers/backend/filter');
require('./helpers/backend/plugin');
require('./helpers/backend/component');

// ===============router=============
import router from "./router/admin";

Vue.component('app', require('./components/BackendApp.vue').default);
// ===============app===============
const app = new Vue({
    el: '#app',
    router,
    data: {
        baseurl: laravel.baseurl,
        submit: false,
        spinner: false,
        loggedIn: false,
        tableSpinner: false,
        pageNumber: 1,
        exception_errors: {},
        validation_errors: {},
        userimage: `${laravel.baseurl}/images/user.png`,
        noimage: `${laravel.baseurl}/images/noimage.png`,
        attach: `${laravel.baseurl}/images/attach.png`,
        institution_id: null,
        menus: [],
        global: [],
        site: {},
        permissions: []
    },
    methods: {
        getInitializeSystems() {
            axios.get('/initialize-systems').then(res => {
                this.site = res.data.site;
                this.menus = res.data.menus;
                this.global = res.data.global;
                this.permissions = res.data.permissions;
                profile.dispatch("setProfile", res.data.profile);
                // console.log(this.global);

            }).catch(err => console.log(err));
        },
        checkPermission(route) {
            let routeName = !route ? this.$route.name : route;
            let check = this.permissions.filter(x => x == routeName);

            return Object.keys(check).length > 0 ? true : false
        },
    },
    mounted() {
        if (Boolean(Admin.loggedIn())) {
            this.getInitializeSystems();
        }
        this.loggedIn = Boolean(Admin.loggedIn());
        this.institution_id = Admin.info().institution_id;
    }
});

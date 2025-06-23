require('./login_bootstrap');
window.Vue = require('vue').default;

require('./helpers/login/AdminPlugin');
require('./helpers/frontend/mixin');

// COMPONENT DEFINE
Vue.component('Login', require('./views/login/Login.vue').default);

// ===============app===============
window.app = new Vue({
    el: '#app',
    data: {
        baseurl: laravel.baseurl,
        app_name: laravel.app_name,
        spinner: false,
        submit: false,
    },
});

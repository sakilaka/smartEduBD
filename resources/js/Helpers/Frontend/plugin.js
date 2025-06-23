import Vue from 'vue';

// ===============Login helpers=============
import User from "../login/User";
window.User = User;

// ===============Spinner===============
import Spinner from './../../components/frontend/elements/Spinner'
Vue.component('Spinner', Spinner)

// ===============Breadcrumbs from vuex===============
import breadcrumbs from "../../vuex/breadcrumbs_frontend";
window.frontBreadcrumbs = breadcrumbs;

// ===============VueLazyload===============
import VueLazyload from 'vue-lazyload'
Vue.use(VueLazyload, {
    listenEvents: ['scroll', 'wheel', 'mousewheel', 'resize', 'animationend', 'transitionend']
})

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);

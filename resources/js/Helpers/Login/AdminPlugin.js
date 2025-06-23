import Vue from 'vue';

// ===============Login helpers=============
import Admin from "./Admin";
window.Admin = Admin;

// ===============Spinner===============
import Spinner from './../../components/frontend/elements/Spinner'
Vue.component('Spinner', Spinner)

// ===============Flash Message===============
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
const options = {
    transition: "Vue-Toastification__fade",
    maxToasts: 2,
    newestOnTop: true,
    position: "top-right",
    timeout: 2000,
};
Vue.use(Toast, options);

// ===============Simple Vue Validator===============
import SimpleVueValidation from "simple-vue-validator";
const Validator = SimpleVueValidation.Validator;
window.Validator = Validator;
Vue.use(SimpleVueValidation);
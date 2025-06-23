import Vue from 'vue';

// ===============Login helpers=============
import Admin from "../login/Admin";
window.Admin = Admin;

// ===============Breadcrumbs from vuex===============
import breadcrumbs from "../../vuex/breadcrumbs";
window.breadcrumbs = breadcrumbs;

// ===============profile from vuex===============
import profile from "../../vuex/profile";
window.profile = profile;

// ===============Flash Message===============
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
const options = {
    transition: "Vue-Toastification__fade",
    // transition: "Vue-Toastification__bounce",
    maxToasts: 2,
    newestOnTop: true,
    position: "top-right",
    timeout: 2000,
};
Vue.use(Toast, options);

// ===============js pdf===============
import jsPDF from 'jspdf';
window.jsPDF = jsPDF;
import autoTable from 'jspdf-autotable'
window.autoTable = autoTable;

// ===============Json Excel===============
import JsonExcel from 'vue-json-excel'
Vue.component('downloadExcel', JsonExcel)

// ===============Simple Vue Validator===============
import SimpleVueValidation from "simple-vue-validator";
const Validator = SimpleVueValidation.Validator;
window.Validator = Validator;
Vue.use(SimpleVueValidation);

// ===============CKEditor===============
import CKEditor from 'ckeditor4-vue';
Vue.use(CKEditor);

// ===============Bootstrap Vue===============
import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);


// ===============Vue Select===============
import VueSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
Vue.component('v-select', VueSelect);

// ===============Vue Datepicker===============
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
Vue.component('DatePicker', DatePicker);
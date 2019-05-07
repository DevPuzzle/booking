/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import moment from 'moment';
import datePicker from 'vue-bootstrap-datetimepicker';
import Notifications from 'vue-notification';
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue);
Vue.use(datePicker,);
Vue.use(Notifications);
window.Vue = require('vue');
Vue.prototype.moment = moment;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.filter('dateFormat', function (date, fromFormat, toFormat) {
    return moment(date, fromFormat).format(toFormat);
});
Vue.component('edit-user-component', require('./components/Settings/EditUser'));
Vue.component('edit-calendar-component', require('./components/Settings/EditCalendars'));
Vue.component('guide-schedule', require('./components/LeaveDays/GuideSchedule'));
Vue.component('admin-guide-schedule', require('./components/LeaveDays/AdminGuideSchedule'));
Vue.component('ck-editor', require('./components/CkEditor'));
Vue.component('ck-editor-edit', require('./components/Page/edit'));
Vue.component('schedule', require('./components/Page/schedule'));
Vue.component('page-create', require('./components/Page/create'));
Vue.component('ck-uploader', require('./components/Page/img_uploader'));
Vue.component('leave-day-list', require('./pages/LeaveDays/LeaveDayList'));
Vue.component('read-by', require('./components/Page/readby'));
Vue.component('timesheet', require('./pages/Timesheets/Timesheet.vue'));
Vue.component('admintimesheet', require('./pages/Timesheets/AdminTimesheet.vue'));
Vue.component('users', require('./components/Users/Users'));

const app = new Vue({
    el: '#app'
});


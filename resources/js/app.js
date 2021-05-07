/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import router from './routes';
import Vuex from 'vuex';
import store from './store';
import vSelect from 'vue-select';
import * as VueGoogleMaps from 'vue2-google-maps';
import Paginate from 'vuejs-paginate'
import Echo from 'laravel-echo';

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyB-tj8rRknadmbisH_GehEwUhlcj0AMJT4',
    libraries: 'places,geometry',
  }
});


Vue.component('v-select', vSelect)
Vue.component('paginate', Paginate)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router: router,
    store: store,
});


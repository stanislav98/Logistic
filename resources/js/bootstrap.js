import Echo from 'laravel-echo';
import store from './store';
import router from './routes';

window._ = require('lodash');


window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    authEndpoint: 'http://localhost:8000/api/broadcast',
    auth: {
        headers: {
            'Authorization': `Bearer ${store.getters.getToken}`,
        }
    },
    forceTLS: false,
});

//every url where user is logged in should assign and add the user to the online users list soooooo no checking for route !!!
// here the magic happens to join on refresh 
// also removing the messages on refresh because the channel is exited

if(store.getters.isDefaultUser === true && store.getters.isLoggedIn === true) {
  store.dispatch('joinAllChannels')
  // store.dispatch('removeMessagesRefresh')
}

//remove notification on refresh of page
store.dispatch('clearNotification')

//make pusher output in console
Pusher.logToConsole = true;

window.axios = require('axios');
if(store.getters.hasToken) {
    window.axios.defaults.headers['Authorization'] = `Bearer ${store.getters.getToken}`
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = "http://localhost:8000/";


 /**
 * Register an Axios HTTP interceptor to add the X-Socket-ID header.
 * This is used to know which socket to broadcast on
 */
window.axios.interceptors.request.use((config) => {
  config.headers['X-Socket-ID'] = window.Echo.socketId() // Echo instance
  // the function socketId () returns the id of the socket connection
  return config
})

/**
  This function is used to handle fallback when token expires.In order to not recieve other errors than 401 
  we logout the user,clear the storage and redirect to Login page! :)
*/
window.axios.interceptors.response.use(function onResponse(response) {
    return response;
  }, async function onResponseError(error) {
    if ("response" in error && "config" in error) { // this is an axios error
      if (error.response.status !== 401) { // can't handle
        return Promise.reject(error);
      }
      //unauthorized i can make a new refreshed token or just put user to logout
        await store.commit('logout');
        localStorage.clear();
        router.push({ name: "Login" })
        // await axios.post('/api/logout').then(() => {
        // })

        // return this.axios.request(config)
      // console.log("401 token refreshinggg");
      // this.token = await this.axios.post("auth", credentials);
      // error.config.headers.authorization = `Bearer ${this.token}`;
      // return this.axios.request(config);
    }
    return error; // not an axios error, can't handler
});

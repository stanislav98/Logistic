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
        // params: {
        //   user_id: store.getters.getUser.id,
        //   name: store.getters.getUser.name,
        //   firm_name: store.getters.getFirm.id
        // },
        headers: {
            'Authorization': `Bearer ${store.getters.getToken}`,
        }
    }
});


Pusher.logToConsole = true;

window.axios = require('axios');
if(store.getters.hasToken) {
    window.axios.defaults.headers['Authorization'] = `Bearer ${store.getters.getToken}`
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = "http://localhost:8000/";

window.axios.interceptors.response.use(function onResponse(response) {
    return response;
  }, async function onResponseError(error) {
    if ("response" in error && "config" in error) { // this is an axios error
      if (error.response.status !== 401) { // can't handle
        return error;
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

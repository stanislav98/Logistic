import Vuex from 'vuex'
import Vue from 'vue'
import createPersistedState from "vuex-persistedstate";
// import * as Cookies from 'js-cookie'
import SecureLS from "secure-ls";
const ls = new SecureLS({encodingType: 'aes', isCompression: false ,encryptionSecret: 'H@sH3D3ENcrY5T3D'});
Vue.use(Vuex)

export default new Vuex.Store({
   plugins: [
     createPersistedState({
      // key: 'vuex',
      storage: {
        getItem: key => ls.get(key),
        setItem: (key, value) => ls.set(key, value),
        removeItem: key => ls.remove(key)
      }
    }),
  ],
  state: {
    status: '',
    firm: [],
    user : [],
    vehicles : [],
    notification: {},
    plans: [],
    invoices: [],
    tovari: [],
    tranport: [],
    tovariTransportTypes: [],
    token: '',
    onlineUsers: [],
  },
  
  getters: {
    isLoggedIn(state) {
        return state.user.length !== 0;
    },
    isAdmin(state){
      return state.user.role_id === 1;
    },
    isDefaultUser(state){
      return state.user.role_id === 2 || state.user.role_id === 5 ;
    },
    hasToken(state){
      return state.token.length !== 0;
    },
    getUser(state) {
      return state.user;
    },
    getToken(state) {
      return state.token;
    },
    isLoggedInAndHasToken(state) {
      return state.user.length !== 0 && state.token.length !== 0
    },

  },
  
  mutations: {
      auth_request(state){
        state.status = 'loading'
      },
      auth_success(state, user){
        state.status = 'success'
        state.user = user
      },
      auth_error(state){
        state.status = 'error'
      },
      logout(state){
        state.status = ''
        state.user = []
        state.firm = []
        state.vehicles = []
        state.firm_fetched = ''
        state.vehicles_fetched = ''
        state.invoices = []
        state.token = ''
        state.onlineUsers = []
      },
      firm_init(state,firm) {
        state.firm = firm
      },
      vehicles_init(state,firm_vehicles) {
        state.vehicles = firm_vehicles
      },
       vehicles_loading(state,vehicles){
         state.vehicles = vehicles
       },
      set_notification(state,notification) {
        state.notification = notification
      },
      set_plans(state,plans) {
        state.plans = plans
      },
      update_user(state,user){
        state.status = 'success'
        state.user = user
      },
      set_invoices(state,invoices) {
        state.invoices = invoices
      },
      set_tovari(state,tovari) {
        state.tovari = tovari
      },
      set_transport(state,transport) {
        state.transport = transport
      },
      set_tovariTransportTypes(state,tovariTransportTypes) {
        state.tovariTransportTypes = tovariTransportTypes
      },
      set_token(state,token) {
        state.token = token
      },
      set_all_users(state,users){
        state.onlineUsers = users
      },
      set_new_online_user(state,user) {
        state.onlineUsers.push(user)
      },
      set_online_user(state,id){
        state.onlineUsers[id].status = 1
      },
      set_offline_user(state,id) {
        state.onlineUsers[id].status = 0
      }
  },
  
  actions: {
    isLoggedIn(state) {
        return state.user.length === 0;
    },
    isInvoicesIn(state) {
      return state.invoices.length === 0;
    },
    set_invoices({commit}) {
      commit('set_invoices')
    }
  },


});
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
    notification: {}, // this is notification on add/delete/load
    token: '',
    activeFriend: null,
    realNotifications: false, // false - no new notifications , true - new notifications
    onlineUsers: [],
    newMessage: {},
    newTovar: {},
    newTransport: {},
  },
  getters: {
    isMainUser(state) {
      return state.user.id === state.user.owner_id
    },
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
    getOnlineUsers(state) {
      return state.onlineUsers;
    },
    isPayed(state) {
      return state.user.has_payed === 1
    }
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
        state.vehicles_fetched = ''
        state.token = ''
        state.onlineUsers = []
        state.activeFriend = null
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
      },
      setActiveFriend(state,activeFriend) {
        state.activeFriend = activeFriend
      },
      setNewMessage(state,message) {
        state.newMessage = message
      },
      setMessages(state,messages){
        state.messages = messages
      },
      removeMessages(state) {
        state.messages = []
      },
      setRealNotifications(state,realNotifications){
        state.realNotifications = realNotifications
      },
      removeRealTimeMessages(state){
        state.realNotifications = false
      },
      newTovar(state,payload) {
        state.newTovar = payload
      },
      newTransport(state,payload) {
        state.newTransport = payload
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
    },
    joinChannelPrivate({commit}){
        Echo.private('private-privatechat.'+this.getters.getUser.id,)
                .listen('.PrivateMessage',(e)=>{
                  const event = e
                 if(window.location.pathname == "/dashboard/chat") {
                    //here im in chat display the message with bold maybe?
                    this.dispatch('sendMessageToUser',event)
                  } else {
                     this.dispatch('sendNotification',event)
                  }
                  // this.commit('setSingleMessageReciever',e.message)
                  // this.activeFriend=e.message.user_id;
                  // this.messages.push(e.message)
                  // this.$store.commit('setSingleMessage',e.message)
                  // setTimeout(this.scrollToEnd,100);
              })
            //   .listenForWhisper('typing', (e) => {
            //     console.log("whisperrr")
            //       // if(e.user.id==this.activeFriend){
            //       //     this.typingFriend=e.user;
                      
            //       //   if(this.typingClock) clearTimeout();
            //       //     this.typingClock=setTimeout(()=>{
            //       //                           this.typingFriend={};
            //       //                       },9000);
            //       // }
            // });
    },
    joinChannelOnline({commit}){
       window.Echo.join('online')
          .here(users => {
                users.forEach(i => {
                    if(i.id != this.getters.getUser.id){
                      var foundIndex = this.getters.getOnlineUsers.findIndex(x => x.id == i.id);
                      this.dispatch('updateUserStatus', {
                        userId: this.getters.getOnlineUsers[foundIndex].id,
                        status: 1
                      });
                    }
                })
          })
          .joining(user => {
            var foundIndex = this.getters.getOnlineUsers.findIndex(x => x.id == user.id);
            this.dispatch('updateUserStatus', {
              userId: this.getters.getOnlineUsers[foundIndex].id,
              status: 1
            });
          })
          .leaving(user => {
            var foundIndex = this.getters.getOnlineUsers.findIndex(x => x.id == user.id);
            this.dispatch('updateUserStatus', {
              userId: this.getters.getOnlineUsers[foundIndex].id,
              status: 0
            });
          })
         .listen('.NewTovar',(e) => {
            //check if we are on page with tovar
            if(window.location.pathname == "/dashboard/tovari") {
              this.dispatch('addNewTov',e.tovar[0])
            }
          })
         .listen('.NewTransport',(e) => {
            if(window.location.pathname == "/dashboard/transport") {
              this.dispatch('addNewTrans',e.transport[0])
            }
         })
    },
    addNewTov({commit},payload) {
      this.commit('newTovar',payload)
    },
    addNewTrans({commit},payload) {
      this.commit('newTransport',payload)
    },
    clearNotification({commit}) {
      this.commit('set_notification',{})
    },
    /** 
      First we get the auth params from the store which will be passed to auth endpoint for pusher
      Then we call funcs to set online/offline users
    */
    joinAllChannels({commit}) {
      const obj =  {
        'user_id': this.getters.getUser.id,
        'name': this.getters.getUser.name,
        'firm_name': this.getters.getUser.company_id
      }
      window.Echo.options.auth.params = obj
      this.dispatch('joinChannelOnline');
      this.dispatch('joinChannelPrivate');
          // this.$router.push({ name:"Profile" });
    },
    updateUserStatus({commit},payload) {
      let foundIndex = this.getters.getOnlineUsers.findIndex(x => x.id == payload.userId);
        if(payload.status == 1) {
          this.commit('set_online_user',foundIndex)
        } else if(payload.status == 0) {
          this.commit('set_offline_user',foundIndex)
        }
    },
    removeMessagesRefresh({commit}) {
      commit('removeMessages');
    },
    sendMessageToUser({commit},payload) { //used to send the message inside chat
      this.commit('setNewMessage',payload)
    },
    sendNotification({commit},payload) { //used to show new notification on user
      this.commit('setRealNotifications',payload);
    }
  },


});
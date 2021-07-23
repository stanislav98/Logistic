
<template>
  <div>
    <div class="card_box filter">
      <div class="card sort_by">
        <input type="text" name="search_user" placeholder="Търсене по име на служител" v-model="filterUserName">
        <input type="text" name="search_firm" placeholder="Търсене по име на фирма" v-model="filterName">
        <button @click.prevent="clearSearch" class="btn">Изчисти търсенето</button>
      </div>
    </div>
    <div class="chat card card_box">
      <div class="online-users">
        <div
          v-for="user in orderedUsers"
          :key="user.id"
          @click="setChannel(user.id)"
          :class="{ 'active': activeFriend === user.id  }"
          class="user"
        >   
            <div class="row">
              <span class="material-icons" v-bind:style="user.status == 1 ? 'color:#5cdb95' : 'color:#f44336'">circle</span>
              <p href="#">{{user.name}}</p>
            </div>
            <p>Фирма : {{ user.firmName}}</p>
        </div>
      </div>
      <div class="messagePickFriend" v-if="!this.activeFriend">
        Моля изберете потребител!
      </div>
      <div id="privateMessageBox" class="messages mb-5" v-if="this.activeFriend">
          <message-list 
          v-bind:all-messages="this.activeMessages"
           v-bind:user ="this.user"
           />
           </message-list>
         <!--  <div class="floating-div">
              <picker v-if="emoStatus" set="emojione" @select="onInput" title="Pick your emoji…" />
          </div> -->
            <div class="input_send">
               <!--  <div class="ml-2 text-right">
                    <a href="#" @click="toggleEmo">
                        <div>insert_emoticon </div>
                    </a>
                </div> -->
                <input type="text" id="message" v-model="message" @keyup.enter="sendMessage" placeholder="Въведете съобщение...">
                <div class="btn_holder">
                    <a href="#" class="sendBtn" @click="sendMessage">Изпрати</a>
                </div>
            </div>
      </div>
    </div>
  </div>
</template>

<script>
  import MessageList from './MessageList'
  import { Picker } from 'emoji-mart-vue'
  export default {
    components:{
      Picker,
      'message-list': MessageList
    },
    
    data () {
      return {
        message:null,
        typingFriend:{},
        typingClock:null,
        emoStatus:false,
        messages:[],
        users:[],
        activeMessages: [],
        filterName: '',
        filterUserName: ''
      }
    },
    computed:{
      activeFriend() {
        return this.$store.state.activeFriend
      },
      friends(){
        return this.users.filter((user)=>{
          return user.id !==this.user.id;
        })
      },
      user() {
          return this.$store.state.user
      },
      onlineUsers () {
          return this.$store.state.onlineUsers
      },
      firm() {
          return this.$store.state.firm
      },
      newMessage() {
          return this.$store.state.newMessage
      },
      orderedUsers() {
        let orderedUsers = this.onlineUsers.sort((a,b) => (a.status < b.status) ? 1 : -1)

        if(this.filterName) {
          orderedUsers = orderedUsers.filter(i => (String(i.firmName).toLowerCase().includes(this.filterName)))
        }

        if(this.filterUserName) {
          orderedUsers = orderedUsers.filter(i => (String(i.name).toLowerCase().includes(this.filterUserName)))
        }

        return orderedUsers
      }
    },
    watch: {
      activeFriend(val){
        this.fetchMessagesUI();
      },
      newMessage(val, oldVal) {
        this.messages.push(val.message)
        this.getMessageById(this.activeFriend)
        setTimeout(this.scrollToEnd,100);
      }
    },
    methods:{
      clearSearch() {
        this.filterName = ''
        this.filterUserName = ''
      },
      setChannel(friend_id){
        this.$store.commit('setActiveFriend',friend_id)
        this.getMessageById(this.activeFriend)
        // if(this.messages.length !== 0) {
        //   typeof this.messages[this.activeFriend] !== 'undefined' && typeof this.messages[this.activeFriend].messages !== 'undefined'? 
        //   this.activeMessages = this.messages[this.activeFriend].messages : this.activeMessages = []
        // } else {
        //   this.activeMessages = []
        // }
        setTimeout(this.scrollToEnd,100);
      },
      onTyping(){
        //on change of input maybe trigger ? ??
        // Echo.private('private-privatechat.3').whisper('typing',{
        //   user:this.user
        // });
      },
      sendMessage(){
        //check if there message
        if(!this.message){
          return alert('Please enter message');
        }
        if(!this.activeFriend){
          return alert('Please select friend');
        }

        const status = this.onlineUsers.find(a => a.id === this.activeFriend);
        axios.post('/api/private-messages/', {message: this.message, sender_id:this.user.id,reciever_id: this.activeFriend, status: status.status}).then(response => {
            this.message=null;
            this.messages.push(response.data.message);
            this.getMessageById(this.activeFriend)
            setTimeout(this.scrollToEnd,100);
        });
      },
      fetchMessagesUI() {
          if(!this.activeFriend){
            return alert('Please select friend');
          }
      },
     
      scrollToEnd(){
        document.getElementById('privateMessageBox').scrollTo({
          top: 99999999999999999999,
          behavior: 'smooth'
        });
      },
      toggleEmo(){
        this.emoStatus= !this.emoStatus;
      },
      onInput(e){
        if(!e){
          return false;
        }
        if(!this.message){
          this.message=e.native;
        }else{
          this.message=this.message + e.native;
        }
        this.emoStatus=false;
      },
      getMessageById(id) {
        let messages = this.messages.filter((el) => {
          return el.sender_id == this.user.id && el.reciever_id == this.activeFriend || el.sender_id == this.activeFriend && el.reciever_id == this.user.id
        })

        this.activeMessages = messages
      }
    },
    mounted(){
      axios.get('/api/private-messages/'+this.user.id)
        .then((response) => {
          this.messages = response.data.messages
          if(this.activeFriend) {
            this.setChannel(this.activeFriend)            
          }
        })
        .catch((error) => {
          console.log(err);
        })
    },
    created(){
      
    }
    
  }
</script>
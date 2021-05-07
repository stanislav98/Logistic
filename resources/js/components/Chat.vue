
<template>
  <div>
    <div v-for="user in onlineUsers">
        {{user.name}} | {{user.status}}
    </div>
    <div class="online-users">
      <div>
         <!--  <div
            v-for="user in onlineUsers"
            :color="(user.status==activeFriend)?'green':'red'"
            :key="user.id"
            @click="setChannel(user.id)"
          > -->
           <div
            v-for="user in onlineUsers"
            :key="user.id"
            @click="setChannel(user.id)"
          >
            <div>
              <div :color="(user.status == 1) ? 'green' : 'red'">
                <span class="material-icons">circle</span>
              </div>
              <a href="#">{{user.name}}</a>
            </div>
          </div>
        </div>

    </div>
    
    <div id="privateMessageBox" class="messages mb-5">
        <message-list 
        v-bind:all-messages="allMessages"
         v-bind:user ="this.user"
         />
         </message-list>
        <div class="floating-div">
            <picker v-if="emoStatus" set="emojione" @select="onInput" title="Pick your emoji…" />
        </div>
        <div>
            <div>
                <div class="ml-2 text-right">
                    <a href="#" @click="toggleEmo">
                        <div>insert_emoticon </div>
                    </a>
                </div>

                <div class="text-center">
                </div>
                <div>
                  <label for="message">Enter message</label>
                  <input type="text" id="message" v-model="message" @keyup.enter="sendMessage">
            
                </div>
                <div>
                    <a href="#" @click="sendMessage" >send</a>
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
        activeFriend:null,
        typingFriend:{},
        onlineFriends:[],
        allMessages:[],
        typingClock:null,
        emoStatus:false,
        users:[],
        token:document.head.querySelector('meta[name="csrf-token"]').content
      }
    },
    computed:{
      friends(){
        return this.users.filter((user)=>{
          return user.id !==this.user.id;
        })
      },
      user () {
          return this.$store.state.user
      },
      onlineUsers () {
          return this.$store.state.onlineUsers
      },
      firm () {
          return this.$store.state.firm
      }, 
    },
    watch:{
      activeFriend(val){
        this.fetchMessages();
      },
    },
    methods:{
      setChannel(friend_id){
        this.activeFriend = friend_id;
        window.Echo.options.auth.params = {
              user_id: this.user.id,
              name: this.user.name,
              firm_name: this.firm.name
          }
          
        Echo.private('privatechat.'+this.activeFriend)
                .listen('PrivateMessage',(e)=>{
                  console.log('pmessage sent')
                  this.activeFriend=e.message.user_id;
                  this.allMessages.push(e.message)
                  setTimeout(this.scrollToEnd,100);
              })
              .listenForWhisper('typing', (e) => {
                  if(e.user.id==this.activeFriend){
                      this.typingFriend=e.user;
                      
                    if(this.typingClock) clearTimeout();
                      this.typingClock=setTimeout(()=>{
                                            this.typingFriend={};
                                        },9000);
                  }
            });
      },
      onTyping(){
        Echo.private('privatechat.'+this.activeFriend).whisper('typing',{
          user:this.user
        });
      },
      sendMessage(){
        //check if there message
        if(!this.message){
          return alert('Please enter message');
        }
        if(!this.activeFriend){
          return alert('Please select friend');
        }
          axios.post('/api/private-messages/'+this.activeFriend, {message: this.message, sender_id:this.user.id,reciever_id: this.activeFriend}).then(response => {
                    this.message=null;
                    this.allMessages.push(response.data.message)
                    setTimeout(this.scrollToEnd,100);
          });
      },
      fetchMessages() {
             if(!this.activeFriend){
              return alert('Please select friend');
            }
            axios.get('/api/private-messages/'+this.user.id+'/'+this.activeFriend).then(response => {
              this.allMessages = response.data.messages;
              setTimeout(this.scrollToEnd,100);
            });
        },
     
      scrollToEnd(){
        document.getElementById('privateMessageBox').scrollTo(0,99999);
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
    },
    mounted(){

    },
    created(){           
    }
    
  }
</script>
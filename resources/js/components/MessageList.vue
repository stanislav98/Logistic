<template>
  <div>
      <div
              class="p-3"
              v-for="(message, index) in allMessages"
              :key="index"
      >

          <div class="message-wrapper">
              <div>
                  <span class="small font-italic">{{message.senderName}}</span>
                  <span class="small font-italic">{{message.recieverName}}</span>
              </div> 


              <div v-if="message.message" class="text-message-container">
                  <div :color="(user.id===message.user_id)?'green':'red'" text-color="white">
                      {{message.message}}
                  </div>

              </div>

          <!--     <div class="image-container">
                  <img v-if="message.image"  :src="'/storage/'+message.image" alt="">

              </div> -->

              <div class="caption font-italic">
                  {{message.created_at}}
              </div>
          </div>


      </div>

  </div>
</template>

<script>
  export default {
    props:['user','allMessages'],
  }
</script>
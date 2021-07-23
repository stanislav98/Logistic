<template>
	<div class="messagesList">
	    <div
	      class="p-3"
	      v-for="(message, index) in allMessages"
	      :key="index"
		>
			<div class="message-wrapper" :class="(user.id===message.sender_id) ? 'sender-wrapper':'reciever-wrapper'">
				<div :class="(user.id===message.sender_id) ? 'sender':'reciever'">
					{{message.message}}
				</div>
				<div class="caption">
					{{ formatDate(message.created_at) }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>
  export default {
    props:['user','allMessages'],
    methods: {
    	formatDate(strDate) {
			return new Date(strDate).toLocaleString();
		},
    }
  }
</script>
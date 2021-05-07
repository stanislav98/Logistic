<template>
	<div class="popup" 
	v-model = "notification.type"
	:class="{'error' :  notification.type == 0, 'success' :  notification.type == 1 }" >
		<span v-model="notification.msg" 	v-if="notification.active === 1" >{{notification.msg}}</span>
		<span @click.prevent="closePopUp" class="close_pop_up" 	v-if="notification.active === 1" >&#10006;</span>
	</div>
</template>
<script>
export default{
	 	props: {
	 		notification: {
	 			type: Object
	 		},

	 	},
		data(){
			return {

			}
		},
		methods: {
			closePopUp() {
				this.$store.commit('set_notification',{});
			}
		},
	}
</script>
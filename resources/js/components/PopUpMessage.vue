<template>
	<div class="popup" 
	v-model = "notification.type"
	:class="{'error' :  notification.type == 0, 'success' :  notification.type == 1 }" >
		<span v-model="notification.msg" 	v-if="notification.active === 1" >{{notification.msg}}</span>
		<span @click.prevent="closeNow" class="close_pop_up" v-if="notification.active === 1" >&#10006;</span>
	</div>
</template>
<script>
export default{
		computed: {
			notification() {
				return this.$store.state.notification
			},
			notificationActive() {
				return this.$store.state.notification.active
			}
		},
		data(){
			return {

			}
		},
		watch: {
			notificationActive(oldM,newM) {
				if(oldM === 1) {
					this.closePopUp();
				}
			}
		},
		methods: {
			closePopUp() {
				setTimeout(() => this.$store.commit('set_notification',{}), 5000);
			},
			closeNow() {
				this.$store.commit('set_notification',{})
			}
		}
	}
</script>
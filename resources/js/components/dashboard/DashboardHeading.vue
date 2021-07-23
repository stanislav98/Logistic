<template>
	<div class="dashboard_heading">
		<div class="left_heading">
			<span>Моят профил - {{user.name}}</span>
		</div>
		<div class="right_heading">
			<div class="notifications" @click="collapseNotifications" :class="{collapse_active:collapse_active == 1}">
				<router-link to="#" class="dash_menu_link">
					<span class="material-icons">notifications</span>
					<span class="notification_count" @v-if="this.notifications">{{ this.notifications.length }}</span>
				</router-link>
				<ul class="notifications_list collapse">
					<li v-for="notifications in this.notifications" :key="notifications.name">
						<a href="#" @click.prevent="setActiveFriendRedirect(notifications.name)">
							{{`Имате ново съобщение от ` + notifications.name }}
						</a>						
					</li>
				</ul>
			</div>
			<div class="menu_options" @click="collapse_active = collapse_active == 2 ? undefined : 2" :class="{collapse_active:collapse_active == 2}">
				<router-link to="#" class="dash_menu_link"><span class="material-icons">person</span></router-link>
				<ul class="collapse links">
					<li><router-link to="/dashboard" class="hovering">Настройки</router-link></li>
					<li><router-link to="#" @click.native="logout" class="hovering">Изход</router-link></li>
				</ul>
			</div>
		</div>
	</div>
</template>
<script>
	import * as Cookies from 'js-cookie'
	export default{
		computed: {
		    user() {
		        return this.$store.state.user
		    },
		    realNotifications() {
		    	return this.$store.state.realNotifications
		    },
		    activeFriend() {
		    	return this.$store.state.activeFriend
		    },
		    onlineUsers() {
		    	return this.$store.state.onlineUsers
		    }
		},
		data(){
			return {
				collapse_active: undefined,
				notifications: [],
				status: 1,
				id: null
			}
		},
		watch: {
			realNotifications(val) {
				const message = val.message
				this.UInsertNotificationMessage(message)
			}
		},
		methods: {
			logout(){
				this.$store.commit('logout');
				Echo.leave('online')
				localStorage.clear();
				axios.post('/api/logout').then(() => {
					this.$router.push({ name:"Home" })
				})
			},
			setActiveFriendRedirect(name){
				this.notifications = []
		    	const activeFriend = this.onlineUsers.find(o => o.name === name);
				this.$router.push({ name:"Chat" })
			},
			collapseNotifications(){
				this.collapse_active = this.collapse_active == 1 ? undefined : 1
				this.status = 1
				this.id = null
				this.updateNotifications()
			},
			updateNotifications(){
				if(this.notifications.length !== 0) {
					axios.post('/api/notifications/',{id: this.user.id, status:this.status, message_id:this.id}).then((resp) => {
						console.log(resp);
					}).catch((err) => {
						console.log(err);
					})
				}
			},
			getNotifications(){
				axios.get('/api/notifications/'+this.user.id)
				.then(resp => {
					this.notifications = resp.data.unreadNotifications
				}).catch((error) => {
					this.$store.commit('set_notification',error.response.data.notification)
				})
			},
		    UInsertNotificationMessage(message) {
		    	this.status = 0
		    	this.id = message.id
		    	this.updateNotifications()
		    	let found = this.notifications.some(ele => ele.name === message.user.name);
		    	if(!found){
		    		this.status=0,
		    		this.notifications.push({
		    			created_at: message.created_at,
		    			name: message.user.name
		    		});
		    	}
		    }
		},
		mounted() {
			this.getNotifications();
		}
	}
</script>
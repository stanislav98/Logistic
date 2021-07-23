<template>
	<!-- 	Dashboard
		<div>{{user.name}}</div>
		<div>{{user.email}}</div>
		<button @click.prevent="logout">Logout</button> -->
		<div class="dashboard_wrapper">
			<dashboard-app></dashboard-app>
			<div class="dashboard_main_content">
				<dashboard-heading></dashboard-heading>
				<router-view></router-view>
				<pop-up-message></pop-up-message>
				<dashboard-footer></dashboard-footer>
			</div>
		</div>
</template>
<script>
	import DashboardSidebar from './DashboardSidebar.vue';
	import DashboardHeading from './DashboardHeading.vue';
	import DashboardFooter from './DashboardFooter.vue';
	import PopUpMessage from '../PopUpMessage.vue';
	import * as Cookies from 'js-cookie'
	export default{
		// data(){
		// 	return {
		// 		notification: this.$store.state.notification,
		// 	}
		// },
		computed: {
		    notification () {
		        return this.$store.state.notification
		    },
		    user () {
		        return this.$store.state.user
		    },
		},
		components: {
			'dashboard-app': DashboardSidebar,
			'dashboard-heading': DashboardHeading,
			'dashboard-footer': DashboardFooter,
			'pop-up-message': PopUpMessage,
		},
		methods:{
		},
		created() {
			if(!this.$store.getters.isLoggedIn) {
				this.$store.commit('logout');
				this.$router.push({ name:"Login" });
			}
		
		},
	}
</script>
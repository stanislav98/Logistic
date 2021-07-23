<template>
	<!-- 	Dashboard
		<div>{{user.name}}</div>
		<div>{{user.email}}</div>
		<button @click.prevent="logout">Logout</button> -->
		<div class="dashboard_wrapper admin">
			<dashboard-app></dashboard-app>
			<div class="dashboard_main_content">
				<dashboard-heading></dashboard-heading>
				<dashboard-main v-if="this.$route.path === '/admin' "></dashboard-main>
				<router-view></router-view>
				<pop-up-message></pop-up-message>
			</div>
		</div>
</template>
<script>
	import DashboardSidebar from './DashboardSidebar.vue';
	import DashboardMain from './DashboardMain.vue';
	import DashboardHeading from './DashboardHeading.vue';
	import PopUpMessage from '../../components/PopUpMessage.vue';
	
	export default{
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
			'dashboard-main': DashboardMain,
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
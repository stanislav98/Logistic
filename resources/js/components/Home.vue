<template>
	<div class="home_page">
		<div class="left">
			<router-link to="/"><img :src="'../images/logo_main.svg'"></router-link>
			<div class="description">
				<about-us v-if="this.$router.app._route.path == '/'"></about-us>
				<router-view></router-view>
				<div class="route_holder" v-if="this.$router.app._route.path == '/'">
					<router-link to="/login" class="btn">Вход</router-link>
					<router-link to="/register" class="btn_second">Регистрация</router-link>
				</div>
			</div>
			<home-footer></home-footer>
			<pop-up-message></pop-up-message>
		</div>
		<div class="right">
			<img :src="'../images/home_bg.svg'">
		</div>
	</div>
</template>
<script>
	import AboutUs from './home/AboutUs.vue';
	// import DashboardHeading from './DashboardHeading.vue';
	import HomeFooter from './home/HomeFooter.vue';
	import PopUpMessage from './PopUpMessage.vue';

	export default {
		components: {
			AboutUs,
			HomeFooter,
			'pop-up-message': PopUpMessage,
		}
	}
</script>
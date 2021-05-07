<template>
	<div class="dashboard_heading">
		<div class="left_heading">
			<button id="minimizeSidebar"><i class="material-icons">more_vert</i></button>
			<span>Моят профил - {{user.name}}</span>
		</div>
		<div class="right_heading">
			<div class="notifications" @click="collapse_active = collapse_active == 1 ? undefined : 1" :class="{collapse_active:collapse_active == 1}">
				<router-link to="#" class="dash_menu_link">
					<span class="material-icons">notifications</span>
					<span class="notification_count">5</span>
				</router-link>
				<ul class="notifications_list collapse">
					<li>Some text</li>
					<li>Some text2</li>
				</ul>
			</div>
			<div class="menu_options" @click="collapse_active = collapse_active == 2 ? undefined : 2" :class="{collapse_active:collapse_active == 2}">
				<router-link to="#" class="dash_menu_link"><span class="material-icons">person</span></router-link>
				<ul class="collapse links">
					<li><router-link to="#" class="hovering">Настройки</router-link></li>
					<li><router-link to="#" class="hovering">Служители</router-link></li>
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
		    user () {
		        return this.$store.state.user
		    },
		},
		data(){
			return {
				collapse_active: undefined,
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
			}
		},
	}
</script>
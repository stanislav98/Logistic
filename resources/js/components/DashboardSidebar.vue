<template>
	<!-- 	Dashboard
		<div>{{user.name}}</div>
		<div>{{user.email}}</div>
		<button @click.prevent="logout">Logout</button> -->
		<div class="dashboard_fix">
			<div class="dashboard_sidebar">
				<div class="head_dashboard aside_section">
					<router-link to="/dashboard" class="logo_wrapper"><img :src="'../images/logo_white.svg'"></router-link>
					<div class="view expanded">
						<div class="hamburger">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>	
				</div>
				<div class="user-info aside_section" 
				@click="collapse_active = collapse_active == 1 ? undefined : 1" :class="{collapse_active:collapse_active == 1}">
					<router-link to="#" class="profile_wrap hovering">
						<span>ИКТ оод</span>
						<span class="material-icons">arrow_drop_down</span>
					</router-link>
					<div class="collapse">
						<router-link to="#" class="hovering">Настройки</router-link>
						<router-link to="#" class="hovering">Служители</router-link>
						<router-link to="#" @click.prevent="logout" class="hovering">Изход</router-link>
					</div>
				</div>
				<ul class="nav aside_section">
					<li class="nav-item active"
					@click="collapse_active = collapse_active == 2 ? undefined : 2" :class="{collapse_active:collapse_active == 2}">
						<router-link to="#" class="hovering with_icon space_between">
							<div class="flex_items">
								<span class="material-icons fi">person</span>
								<span>Примерен линк</span>
							</div>
							<span class="material-icons">arrow_drop_down</span>
						</router-link>
						<div class="collapse">
							<ul class="submenu">
								<li><router-link to="#" class="hovering">Събменю</router-link></li>	
							</ul>
						</div>
					</li>
					<li class="nav-item active">
						<router-link to="/dashboard/payment" class="hovering with_icon">
							<span class="material-icons fi">person</span>
							Плащане
							<span class="material-icons"></span>
						</router-link>
					</li>
					<li class="nav-item active">
						<router-link to="/dashboard/invoices" class="hovering with_icon">
							<span class="material-icons fi">person</span>
							Фактури
							<span class="material-icons"></span>
						</router-link>
					</li>
					<li class="nav-item active">
						<router-link to="/dashboard/tovari" class="hovering with_icon">
							<span class="material-icons fi">person</span>
							Товари
							<span class="material-icons"></span>
						</router-link>
					</li>
					<li class="nav-item active">
						<router-link to="/dashboard/chat" class="hovering with_icon">
							<span class="material-icons fi">person</span>
							Чат
							<span class="material-icons"></span>
						</router-link>
					</li>
				</ul>
				</div>
			</div>
		</div>
</template>
<script>
	export default{
		data(){
			return {
				collapse_active: undefined
			}
		},
		methods:{
			logout(){
				axios.post('/api/logout').then(() => {
					this.$router.push({ name:"Home" })
				})
			}
		},
		mounted(){
			// axios.get('/api/user').then((res) => {
			// 	this.user = res.data;
			// })
		}
	}
</script>
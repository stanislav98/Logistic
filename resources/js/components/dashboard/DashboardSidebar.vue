<template>
		<div class="dashboard_fix">
			<div class="dashboard_sidebar">
				<div class="head_dashboard aside_section">
					<router-link to="/dashboard" class="logo_wrapper"><img :src="'/images/logo_white.svg'"></router-link>
					<div class="hamburger" @click="showOnMobile = !showOnMobile">
						<span></span>
						<span></span>
						<span></span>
					</div>
				</div>
				<div class="user-info aside_section" :class="{'mobile_show': showOnMobile, 'mobile_hide': !showOnMobile}">
					<router-link :to="`${mainMenu.url}`" class="profile_wrap hovering">
						<span>{{ this.firm.name }}</span>
						<span class="material-icons"
						@click="mainMenu.collapse_active = !mainMenu.collapse_active"
						>{{ mainMenu.collapse_active !== true ? 'arrow_drop_down' : 'arrow_drop_up' }}</span>
					</router-link>
					<div class="collapse_active" v-if="mainMenu.collapse_active === true">
						<router-link to="/dashboard/my-users" class="hovering mainMenu" v-if="this.$store.getters.isMainUser">Служители</router-link>
						<router-link :to="`${menu.url}`" class="hovering mainMenu" v-for="menu in mainMenu.submenu" :key="menu.url">{{menu.text}}</router-link>
						<router-link to="#" v-on:click.native="logout" class="hovering mainMenu">Изход</router-link>
					</div>
				</div>
				<ul class="nav aside_section" :class="{'mobile_show': showOnMobile, 'mobile_hide': !showOnMobile}">
					<li class="nav-item active" v-for="menu in menuArr" :key="menu.url">
						<router-link :to="`${menu.url}`" class="hovering with_icon space_between">
							<div class="flex_items">
								<span class="material-icons fi">{{ menu.icon }}</span>
								<span>{{ menu.text }}</span>
							</div>
							<span class="material-icons" 
								v-if="getSubMenuStatus(menu.submenu)" 
								@click="menu.collapse_active = !menu.collapse_active"
							> {{ menu.collapse_active !== true ? 'arrow_drop_down' : 'arrow_drop_up' }} </span>
						</router-link>
						<div class="collapse_active" v-if="menu.collapse_active === true">
							<ul class="submenu" v-if="getSubMenuStatus(menu.submenu)">
								<li v-for="submenu in menu.submenu">
									<router-link :to="`${submenu.url}`" class="hovering">{{ submenu.text }}</router-link>
								</li>	
							</ul>
						</div>
					</li>
					<li class="nav-item active" v-if="this.$store.getters.isMainUser">
						<router-link to="/dashboard/invoices" class="hovering with_icon space_between">
							<div class="flex_items">
								<span class="material-icons fi">description</span>
								<span>Фактури</span>
							</div>
						</router-link>
					</li>
					<li class="nav-item active" v-if="this.$store.getters.isMainUser">
						<router-link to="/dashboard/payment" class="hovering with_icon space_between">
							<div class="flex_items">
								<span class="material-icons fi">payments</span>
								<span>Абонамент</span>
							</div>
						</router-link>
					</li>
				</ul>
				</div>
			</div>
		</div>
</template>
<script>
	export default{
		computed: {
			firm() {
				return this.$store.state.firm
			},
			user() {
				return this.$store.state.user
			}
		},
		data(){
			return {
				showOnMobile: false,
				mainMenu: {
					'icon' : 'person',
					// 'text' : this.firm.name,
					'url' : '#',
					'submenu' : [
						{
							'text' : 'Товари',
							'url' : '/dashboard/my-tovari'
						},
						{
							'text' : 'Транспорт',
							'url' : '/dashboard/my-transport'
						},

					],
					'collapse_active' : false
				},
				menuArr: [
					{
						'icon' : 'rv_hookup',
						'text' : 'Товари',
						'url' : '/dashboard/tovari',
						'submenu' : {},
						'collapse_active' : false
					},
					{
						'icon' : 'local_shipping',
						'text' : 'Транспорт',
						'url' : '/dashboard/transport',
						'submenu' : {},
						'collapse_active' : false
					},
					{
						'icon' : 'question_answer',
						'text' : 'Чат',
						'url' : '/dashboard/chat',
						'submenu' : {},
						'collapse_active' : false
					},
					{
						'icon' : 'business',
						'text' : 'Фирми',
						'url' : '/dashboard/firms',
						'submenu' : {},
						'collapse_active' : false
					},
					{
						'icon' : 'person',
						'text' : 'Потребители',
						'url' : '/dashboard/users',
						'submenu' : {},
						'collapse_active' : false
					},
					{
						'icon' : 'add_road',
						'text' : 'Новини',
						'url' : '/dashboard/news',
						'submenu' : {},
						'collapse_active' : false
					},
				]
			}
		},
		methods:{
			getSubMenuStatus(obj) {
				return obj && Object.keys(obj).length !== 0
			},
			logout(){
				this.$store.commit('logout');
				Echo.leave('online')
				localStorage.clear();
				axios.post('/api/logout').then(() => {
					this.$router.push({ name:"Home" })
				})
			}
		}
	}
</script>
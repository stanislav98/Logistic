<template>
	<div class="login login_page">
		<div class="right">
			<div class="topbar">
				<div class="container">
					<router-link to="/"><img :src="'../images/logo_main.svg'"></router-link>
					<div class="right">
						<router-link to="/register" class="btn">Регистрация</router-link>
					</div>
			</div>
			</div>
			<h1 class="center">Вход</h1>
			<div class="form_wrapper">
				<div class="input_holder" :class="{'error' : errors.email }">
					<div class="wrap_elements">
						<span class="material-icons">email</span>
						<input type="email" name="email" placeholder="Имейл" v-model="form.email">
					</div>
					<span class="error" v-if="errors.email">{{errors.email[0]}}</span>
				</div>
				<div class="input_holder" :class="{'error' : errors.password }">
					<div class="wrap_elements">
						<span class="material-icons">https</span>
						<input type="password" name="password" placeholder="Парола" v-model="form.password">
					</div>
					<span class="error" v-if="errors.password">{{errors.password[0]}}</span>
				</div>
			</div>	
			<div class="center mb-24 wrap_login">
				<button @click.prevent="loginUser" class="btn_second" type="submit">Вход</button>
				<router-link to="/reset-password">Забравена парола?</router-link>
			</div>
		
		</div>
		<div class="left">
			<img :src="'../images/login.svg'">
		</div>
	</div>
</template>
<script>
	export default{
		computed: {
		    user() {
		        return this.$store.state.user
		    },
		    onlineUsers() {
		          return this.$store.state.onlineUsers
		    },
		    firm() {
		        return this.$store.state.firm
		    }, 
		    vehicles() {
		    	return this.$store.state.vehicles
		    }, 
		},
		data(){
			return {
				form: {
					email: '',
					password: '',
				},
				errors:[]
			}
		},
		methods: {
			setAllUsers(allUsers) {
				if(this.onlineUsers.length === 0) {
				        allUsers.sort((a,b) => (a.status > b.status) ? 1 : ((b.status < a.status) ? -1 : 0))
				        this.$store.commit('set_all_users',allUsers)
				}
			},
			initFirmAndData(firm,firm_vehicles) {
				if(this.vehicles.length === 0 ) {
					this.$store.commit('vehicles_init',firm_vehicles)
				}
				if(this.firm.length === 0 ) {
					this.$store.commit('firm_init',firm)
				}
			},
			initUser(user, token) {
				        this.$store.commit('auth_success',user)
				        this.$store.commit('set_token',token)
				        window.axios.defaults.headers['Authorization'] = `Bearer ${token}`
			},
			loginUser({commit}){
		        this.$store.commit('auth_request')
		        this.errors = []
		        axios.post('/api/login',this.form).then((userResp) => {
		        	const user = userResp.data.user
       				const token = userResp.data.token
       				this.initUser(user,token)

       				const vehicles = userResp.data.vehicles
					const firm = userResp.data.firm
					this.initFirmAndData(firm,vehicles)

					const allUsers = userResp.data.users
					this.setAllUsers(allUsers)

					this.$store.dispatch('joinAllChannels')
					this.$router.push({ name:"Profile" });

		        }).catch((error) => {

		        	console.log(error.response)
		        	// console.log(error.response.errors)
		        	this.errors = error.response.data.errors
		        })
			}
		}
	}
</script>
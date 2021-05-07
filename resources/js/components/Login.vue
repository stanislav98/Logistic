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
						<input type="email" name="email" placeholder="Парола" v-model="form.email">
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
			<div class="center mb-24">
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
		    user () {
		        return this.$store.state.user
		    },
		    onlineUsers () {
		          return this.$store.state.onlineUsers
		    },
		    firm () {
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
			updateUserStatus(id,type) {
				let foundIndex = this.onlineUsers.findIndex(x => x.id == id);
				if(type == 1) {
				  this.$store.commit('set_online_user',foundIndex)
				} else if(type == 0) {
				  this.$store.commit('set_offline_user',foundIndex)
				}
			},
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
				axios.all([
					axios.post('/api/login',this.form), 
					axios.get(`/api/vehicles/${this.user.company_id}`),
					axios.post(`/api/users/`,this.user.id)
				])
				.then(axios.spread((userResp, firmResp, onlineUsersResp) => {
					const user = userResp.data.user
					const token = userResp.data.token

					const vehicles = firmResp.data.vehicles
					const firm = firmResp.data.firm

					const allUsers = onlineUsersResp.data.users

					this.initUser(user,token)
					this.initFirmAndData(firm,vehicles)
					this.setAllUsers(allUsers)

					window.Echo.options.auth.params = {
							user_id: this.user.id,
							name: this.user.name,
							firm_name: this.firm.name
					}
							
			      	Echo.join('online')
					.here(users => {
				        users.forEach(i => {
				            if(i.id != this.user.id){
				              var foundIndex = this.onlineUsers.findIndex(x => x.id == i.id);
				              this.updateUserStatus(this.onlineUsers[foundIndex].id,1)
				            }
				      	})
					})
					.joining(user => {
						this.updateUserStatus(user.id,1)
					})
					.leaving(user => {
						this.updateUserStatus(user.id,0)
					}) 

					this.$router.push({ name:"Profile" });

				}))
				.catch((error) => {
				    this.$store.commit('auth_error')
				    console.log(error)
					if (error.response.status == 422) {
			           this.errors = error.response.data.errors;
			         } else {
			           this.errors.push("Нещо се обърка, моля опитайте отново!");
			         }
				});

			}
		}
	}
</script>

<!-- this.$router.push({ name:"Profile" }); -->
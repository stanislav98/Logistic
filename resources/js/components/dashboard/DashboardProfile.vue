<template>
	<div class="redenred_dashboard">
		<div class="card_box">
			<div class="card">
				<div class="form_wrapper" >
					<div class="input_holder disabled">
						<div class="wrap_elements">
							<span class="material-icons">business</span>
							<input type="text" name="bulstat" placeholder="Булстат" :disabled="1" v-model="firm.bulstat">
						</div>
					</div>
					<div class="input_holder disabled">
						<div class="wrap_elements">
							<span class="material-icons">person</span>
							<input type="text" name="name" placeholder="Име" :disabled="1" v-model="user.name">
						</div>
					</div>
					<div class="input_holder" :class="{'error' : errors.phone_number }">
						<div class="wrap_elements">
							<span class="material-icons">phone</span>
							<input type="text" name="phone_number" placeholder="Телефонен номер" v-model="newPhone">
						</div>
						<span  class="error" v-if="errors.phone_number">{{errors.phone_number[0]}}</span>
					</div>
					<div class="input_holder" :class="{'error' : errors.email }">
						<div class="wrap_elements">
							<span class="material-icons">email</span>
							<input type="email" name="email" placeholder="Имейл" v-model="newEmail">
						</div>
						<span class="error" v-if="errors.email">{{errors.email[0]}}</span>
					</div>
				</div>
				<div class="center">
					<button class="btn" @click.prevent="updateUser">Запази промените</button>
				</div>
			</div>
		</div>
		<div class="card_box">
			<div class="card">
				<div class="form_wrapper">
					<vehicle-form
					v-for="item in vehicles"
	       			v-bind:key="item.id"
	       			v-bind:vehicle ="item"
					></vehicle-form>
				</div>
				<div class="center">
					<button class="btn" @click.prevent="updateVehiclesByCompany">Запази промените</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import VehiclesForm from '../VehiclesForm.vue';
	export default{
		computed: {
		    firm () {
		        return this.$store.state.firm
		    }, 
		    user () {
		        return this.$store.state.user
		    },
		    onlineUsers() {
		    	return this.$store.state.onlineUsers
		    },
		    vehicles () {
		    	return this.$store.state.vehicles
		    },
	        vehicles_fetched () {
		    	return this.$store.state.vehicles_fetched
		    },
		    firm_fetched () {
		    	return this.$store.state.firm_fetched
		    }
		},
		data(){
			return {
				errors:[],
				oldEmail: '',
				oldPhone: '',
				newPhone: '',
				newEmail: '',
			}
		},
		methods:{
			updateUserStatus(id,type) {
				let foundIndex = this.onlineUsers.findIndex(x => x.id == id);
				if(type == 1) {
				  this.$store.commit('set_online_user',foundIndex)
				} else if(type == 0) {
				  this.$store.commit('set_offline_user',foundIndex)
				}
			},
			updateUser() {
				let data = {}

				if(this.oldEmail.localeCompare(this.newEmail)) {
					data.email = this.newEmail
				}

				if(this.oldPhone.localeCompare(this.newPhone)) {
					data.phone_number = this.newPhone
				}

				axios.put(`/api/user/${this.user.id}`,data).then((response) => {
					const user = response.data.user
					const notification = response.data.notification

					this.errors = []
					this.oldEmail = this.user.email
					this.oldPhone = this.user.phone_number
			        this.$store.commit('auth_success',user)
			        this.$store.commit('set_notification',notification)
			        
				}).catch((error) => {
					if(error.response.data.notification) {
						this.$store.commit('set_notification',error.response.data.notification)
					} else {
						const errors = error.response.data.errors
						const msg = errors[Object.keys(errors)[0]][0]
						const errorObj = {
							'msg': msg ,
		        			'type': 0, 
		        			'active': 1
						}
						
						this.$store.commit('set_notification', errorObj)
						this.errors = errors;
					}
				})
			},
			updateVehiclesByCompany() {
				axios.put(`/api/vehicles/${this.firm.id}`,{firm : this.firm, vehicles: this.vehicles}).then((res) => {
			        const notification = res.data.notification
					this.$store.commit('set_notification',notification)
				}).catch((error) => {
					this.errors = error.response.data.errors;
				})
			}
		},
		components: {
			'vehicle-form': VehiclesForm,
		},
		created() {
			this.oldEmail = this.user.email
			this.newEmail = this.user.email
			this.oldPhone = this.user.phone_number
			this.newPhone = this.user.phone_number
		},
		mounted(){
		}	
	}
</script>
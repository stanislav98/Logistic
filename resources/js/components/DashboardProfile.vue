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
							<input type="text" name="phone_number" placeholder="Телефонен номер" v-model="user.phone_number">
						</div>
						<span  class="error" v-if="errors.phone_number">{{errors.phone_number[0]}}</span>
					</div>
					<div class="input_holder" :class="{'error' : errors.email }">
						<div class="wrap_elements">
							<span class="material-icons">email</span>
							<input type="email" name="email" placeholder="Имейл" v-model="user.email">
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
	import VehiclesForm from './VehiclesForm.vue';
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
				axios.put(`/api/user/${this.user.id}`,this.user).then((response) => {
					const user = response.data.user
					const notification = response.data.notification
			        this.$store.commit('auth_success',user)
			        this.$store.commit('set_notification',notification)
			        
				}).catch((error) => {
					this.errors = error.response.data.errors;
				})
			},
			updateVehiclesByCompany() {
				axios.put(`/api/vehicles/${this.firm.id}`,{firm : this.firm, vehicles: this.vehicles}).then((res) => {
			        // const firm_vehicles = res.data.vehicles
			        const notification = res.data.notification
					// this.$store.commit('vehicles_init',firm_vehicles)
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
				
		},
		mounted(){
		}	
	}
</script>
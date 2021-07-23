<template>
	<div class="wrap_single_firm">	
		<div class="single-firm card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>{{ firm.name }}</h1>
					<rating></rating>
				</div class="header_firm">
				<p class="bulstat"><span class="material-icons">business</span><span>Булстат</span> : {{ firm.bulstat }}</p>
				<p class="manager"><span class="material-icons">person</span><span>Мениждър</span> : {{ firm.manager }}</p>
				<p class="address"><span class="material-icons">home</span><span>Адрес</span> : {{ firm.address }}</p>
				<div class="grid_two" v-if="loaded === true">
					<ul class="employees">
						<p class="ul_title">Служители</p>
						<li v-for="user in getUserDetails()">
							<p class="name">
								<span class="material-icons">person</span>
								<span>Име</span> : 
								<a :href="`/dashboard/users/${user.id}`">&nbsp;{{ user.name }}</a>
							</p>							
							<p class="email">
								<span class="material-icons">email</span>
								<span>Имейл</span> : 
								<a :href="`mailto:${user.email}`">&nbsp;{{ user.email }}</a>
							</p>							
							<p class="phone">
								<span class="material-icons">phone</span>
								<span>Телефонен номер</span> : 
								<a :href="`tel: ${user.phone}`">&nbsp;{{ user.phone }}</a>
							</p>							
						</li>
					</ul>
					<ul class="vehicles">
						<p class="ul_title">Автопарк</p>
						<li v-for="vehicle in getVehiclesDetails()">
							<p class="vehicle">
								<span class="material-icons ">local_shipping</span>
								<span>{{ vehicle.name }} : {{ vehicle.count }}</span>
							</p>							
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="grid_two">
				<comments></comments>
				<reports></reports>
			
		</div>
	</div>
</template>
<script>
	import Rating from './single-firm/Rating.vue';
	import Comments from './single-firm/Comments.vue';
	import Report from './single-firm/Report.vue';
	export default {
		data(){
			return {
				firm: {},
				loaded: false
			}
		},
		components: {
			'rating': Rating,
			'comments': Comments,
			'reports': Report,
		},
		methods: {
			getUserDetails(){
				let users = this.firm.users.split(',')
				let phone = this.firm.phones.split(',')
				let email = this.firm.emails.split(',')
				let user_ids = this.firm.user_ids.split(',')
				let result = {
					users: []
				}

				for (var i = 0; i < users.length; i++){
					let obj = {
						"name": users[i],
						"phone": phone[i],
						"email": email[i],
						"id": user_ids[i]
					}
					result.users.push(obj)				
				}

		    	return result.users;
			},
			getVehiclesDetails(){
				let count = this.firm.vehicle_counts.split(',')
				let vehicles = this.firm.vehicle_names.split(',')

				let result = {
					vehicles: []
				}

				for (var i = 0; i < vehicles.length; i++){
					let obj = {
						"name": vehicles[i],
						"count": count[i],
					}
					result.vehicles.push(obj)				
				}

		    	return result.vehicles;
			},
		},
		created() {
			axios.get(`/api/firms/${this.$route.params.id}`)
			.then(resp => {
				this.firm = resp.data.firm
				this.loaded = true
				this.getUserDetails()
			})
			.catch(err => {
				if(err.response) {
					this.$store.commit('set_notification',err.response.data.notification)
				}
			})
		}

	}
</script>
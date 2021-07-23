<template>
	<li class="card_box admin_card_box">
		<div class="pad_wrapper">
			<div class="header_firm" :class="user.path ? 'unauth' : ''">
				<h4>{{ user.name }}</h4>
				<div>
					<button @click.prevent="toDelete(user.id)" class="btn_second danger">Изтрий</button>
					<button @click.prevent="aproveUser(user.id)" class="btn_second green ml-10" v-if="user.path">Одобри</button>
				</div>
			</div class="header_firm">
			<div class="user_details">
				<p class="firm">
					<span class="material-icons">business</span>
					<span>Фирма</span>
					<span>&nbsp;{{ user.firmName }} | {{ user.bulstat }}</span>
				</p>
				<p class="email">
					<span class="material-icons">email</span>
					<span>Имейл</span>
					<a :href="`mailto:${user.email}`">&nbsp;{{ user.email }}</a>
				</p>							
				<p class="phone">
					<span class="material-icons">phone</span>
					<span>Телефонен номер</span> : 
					<a :href="`tel: ${user.phone_number}`">&nbsp;{{ user.phone_number }}</a>
				</p>
				<p class="status">
					<span class="material-icons">assignment_ind</span>
					Статус на профила
					<span>&nbsp;{{ user.has_payed === 1 ? 'Активен' : 'Неактивен' }}</span>
				</p>
				<p class="role">
					<span class="material-icons">badge</span>
					Длъжност
					<span>&nbsp;{{ user.roleName }}</span>
				</p>
				<a :href="`${'/storage/'+user.path}`" target="_blank" class="btn mt-10 small" v-if="user.path">Виж снимката</a>	
			</div>
		</div>
	</li>
</template>
<script>
export default{
	 	props: {
	 		user: {
	 			type: Object
	 		},

	 	},
		data(){
			return {
			}
		},
		methods: {
			aproveUser(id) {
				axios.put(`/api/users/unauthorized`,this.user)
				.then(resp => {
					const idx =  this.$parent.allUsers.map(e => e.id).indexOf(this.user.id);
					const unauthIdx = this.$parent.unAuthorizedUsers.indexOf(this.user)
					this.$parent.unAuthorizedUsers.splice(unauthIdx, 1)
					this.$parent.allUsers[idx].has_payed = 1;
					this.$parent.page = 1;
					this.$emit('clicked',1)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете този потребител ?")) {
		    		axios.delete(`/api/users/${id}`)
					.then(resp => {
						const idx = this.$parent.sortedUsers.indexOf(this.user)
						this.$parent.sortedUsers.splice(idx, 1)
						this.$parent.page = 1
					})
					.catch(err => {
						this.$store.commit('set_notification',err.response.data.notification)
					})
		    	}
		    },
		}
	}
</script>
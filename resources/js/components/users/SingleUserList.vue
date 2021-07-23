<template>
	<li class="card_box" >
		<div class="pad_wrapper">
			<div class="header_firm">
				<h4><a :href="`/dashboard/users/${user.id}`">{{ user.name }}</a></h4>
				<div class="grid_btns">
					<button @click.prevent="toProfile(user.id)" class="btn">Към Профила</button>
					<button @click.prevent="toChat(user.id)" class="btn_second">Към Чата</button>
					<button @click.prevent="toEdit(user.id)" class="btn_second" v-if="user.canEdit === 1">Редактирай</button>
					<button @click.prevent="toDelete(user.id)" class="btn_second danger" v-if="user.canEdit === 1">Изтрий</button>
				</div>
				
			</div class="header_firm">
			<div class="user_details">
				<p class="firm">
					<span class="material-icons">business</span>
					Фирма : 
					<a :href="`/dashboard/firms/${user.firmId}`">&nbsp;{{ user.firmName }} | {{ user.bulstat }}</a>
				</p>
				<p class="email">
					<span class="material-icons">email</span>
					<span>Имейл</span> : 
					<a :href="`mailto:${user.email}`">&nbsp;{{ user.email }}</a>
				</p>							
				<p class="phone">
					<span class="material-icons">phone</span>
					<span>Телефонен номер</span> : 
					<a :href="`tel: ${user.phone_number}`">&nbsp;{{ user.phone_number }}</a>
				</p>
				<p class="status">
					<span class="material-icons">assignment_ind</span>
					Статус на профила : 
					<span>&nbsp;{{ user.has_payed === 1 ? 'Активен' : 'Неактивен' }}</span>
				</p>
				<p class="role">
					<span class="material-icons">badge</span>
					Длъжност : 
					<span>&nbsp;{{ user.roleName }}</span>
				</p>		
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
			toProfile(id) {
		    	this.$router.push({ path: `/dashboard/users/${id}` });
		    },
		    toEdit(id) {
		    	this.$emit('edited',id)
		    	this.$emit('clicked', 2)
		    },
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете този потребител ?")) {
		    		axios.delete(`/api/users/${id}`)
					.then(resp => {
						const idx = this.$parent.relUsers.indexOf(this.user)
						this.$parent.relUsers.splice(idx, 1)
						this.$parent.page = 1
					})
					.catch(err => {
						this.$store.commit('set_notification',err.response.data.notification)
					})
		    	}
		    },
		    toChat(id) {
		    	this.$store.commit('setActiveFriend',id)
		    	this.$router.push({ path: `/dashboard/chat` })
		    },
		}
	}
</script>
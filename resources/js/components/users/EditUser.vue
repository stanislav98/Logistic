<template>
	<div class="wrap_single_user">	
		<div class="single-user card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>{{ user.name }}</h1>
					<div class="grid_btns">
						<button @click.prevent="toProfile(user.id)" class="btn">Към Профила</button>
						<button @click.prevent="toChat(user.id)" class="btn_second">Към Чата</button>
						<button @click.prevent="toView()" class="btn_second">Назад</button>
						<button @click.prevent="toDelete(user.id)" class="btn_second danger">Изтрий</button>
					</div>
				</div class="header_user">
				<div class="user_details">
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
				<div class="center">
					<button class="btn" @click.prevent="updateUser">Запази промените</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import { Carousel, Slide } from 'vue-carousel';
	import SingleUserList from './SingleUserList.vue';

	export default {
		props: {
			user: {
				type: Object
			}
		},
		data(){
			return {
				relatedUsers: [],
				errors: [],
				oldEmail: '',
				newEmail: '',
				oldPhone: '',
				newPhone: '',

			}
		},
		components: {
		    Carousel,
		    Slide,
		    'single-user-list': SingleUserList
		},
		methods: {
			toProfile(id) {
		    	this.$router.push({ path: `/dashboard/users/${id}` });
		    },
		    toChat(id) {
		    	this.$store.commit('setActiveFriend',id)
		    	this.$router.push({ path: `/dashboard/chat` })
		    },
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете този потребител ?")) {
		    		axios.delete(`/api/users/${id}`)
					.then(resp => {
						const idx = this.$parent.relUsers.indexOf(this.user)
						this.$parent.relUsers.splice(idx, 1)
						this.$parent.page = 1
						this.$emit('edited',0)
		    			this.$emit('clicked',1)
					})
					.catch(err => {
						this.$store.commit('set_notification',err.response.data.notification)
					})
		    	}
		    },
		    toView() {
		    	this.$emit('edited',0)
		    	this.$emit('clicked',1)
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
					const userEmail = response.data.user.email
					const userPhone = response.data.user.phone_number
					const notification = response.data.notification

					this.user.email = userEmail
					this.user.phone_number = userPhone

					this.errors = []
					this.oldEmail = this.user.email
					this.oldPhone = this.user.phone_number

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
		},
		created() {
			this.oldEmail = this.user.email
			this.newEmail = this.user.email
			this.oldPhone = this.user.phone_number
			this.newPhone = this.user.phone_number
		}

	}
</script>
<template>
	<div class="wrap_single_user">	
		<div class="single-user card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>{{ user.name }}</h1>
					<button @click.prevent="toChat(user.id)" class="btn_second">Към Чата</button>
				</div class="header_user">
				<div class="user_details">
					<p class="firm">
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
		</div>
		<div class="related_users" v-if="relatedUsers.length !== 0">
			<h4 class="h1_title mb-12">Други потребители от фирмата</h4>
			<ul class="single-firm">
				<carousel>
					<slide v-for="user in relatedUsers" :key="user.id">
						<single-user-list
		       			v-bind:key="user.id"
		       			v-bind:user ="user"
						></single-user-list>
					</slide>
				</carousel>
			</ul>
		</div>
	</div>
</template>
<script>
	import { Carousel, Slide } from 'vue-carousel';
	import SingleUserList from './SingleUserList.vue';

	export default {
		data(){
			return {
				user: {},
				relatedUsers: []
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
		    }
		},
		created() {
			axios.get(`/api/user/${this.$route.params.id}`)
			.then(resp => {
				this.user = resp.data.user

				axios.get(`/api/relatedusers/${this.user.firmId}`)
				.then(resp => {
					if(resp.data.hasRel === 1) {
						this.relatedUsers = resp.data.relatedUsers.filter(o => o.id !== this.user.id)
					}
				})
				.catch(err => {
					const errorObj = {
						'msg': "Нещо се обърка ! Моля презаредете страницата" ,
	        			'type': 0, 
	        			'active': 1
					}
					this.$store.commit('set_notification', errorObj)
				})
			})
			.catch(err => {
				if(err.response) {
					this.$store.commit('set_notification',err.response.data.notification)
				}
			})
		}

	}
</script>
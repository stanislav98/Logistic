<template>
	<div>
		<ul class="list_actions">
			<li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
				Преглед на потребители
			</li>
			<li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}"
				v-if="relatedUsers.length !== 0">
				Редактиране на потребители
			</li>
			<li @click.prevent="activate(3)" :class="{ 'btn': active_el === 3, 'btn_second': active_el !== 3}">
				Добавяне на потребители
			</li>
		</ul>
		<!-- PREVIEW -->
		<div class="related_users" v-if="active_el === 1">
			<h1 class="h1_title mb-12">Вашите служители</h1>
			<ul class="single-firm grid_two" v-if="relatedUsers.length !== 0">
					<single-user-list
					v-for="user in relatedUsers"
	       			:key="user.id"
	       			v-bind:user ="user"
	       			@clicked="activate"
	       			@edited="setEditId">
	       			</single-user-list>
			</ul>
			<paginate
		    v-model="page"
		    :page-count="pagesCount"
		    :page-range="3"
		    :margin-pages="2"
		    :prev-text="'&#8592;'"
		    :next-text="'&#8594;'"
		    :hide-prev-next="true"
		    :container-class="'pagination'"
		    :page-class="'page-item'"
		    v-if="relatedUsers.length !== 0">
		</paginate>
			<div v-else>
				<h4>Няма намерени потребители !</h4>
			</div>
		</div>
		<!-- Edit User -->
		<div class="edit_user" v-if="active_el === 2 && edit_id !== 0">
			<edit-user :user="getUserById(edit_id)" @clicked="activate"></edit-user>
		</div>
		<!-- Add User -->
		<div class="add_user" v-if="active_el === 3">
			<add-user></add-user>
		</div>
		
	</div>
</template>
<script>
	import { Carousel, Slide } from 'vue-carousel';
	import SingleUserList from './SingleUserList.vue';
	import EditUser from './EditUser.vue';
	import AddUsers from './AddUsers.vue';

	export default {
		computed: {
			firm() {
				return this.$store.state.firm
			},
			user() {
				return this.$store.state.user
			},
			relatedUsers() {
				let sorted_users = this.relUsers;
				
		        this.setPages(sorted_users);
	        	sorted_users = this.paginate(sorted_users);

	        	return sorted_users
			}
		},
		data(){
			return {
				relUsers: [],
				active_el: 1,
				edit_id: 0,
				perPage: 2,
			    page: 1,
			    pages: [],
			    pagesCount: 0,
			}
		},
		components: {
		    Carousel,
		    Slide,
		    'edit-user': EditUser,
		    'single-user-list': SingleUserList,
		    'add-user': AddUsers
		},
		methods: {
			paginate (users) {
		      let page = this.page;
		      let perPage = this.perPage;
		      let from = (page * perPage) - perPage;
		      let to = (page * perPage);
		      return users.slice(from, to);
		    },
		    nextPage() {
				if((this.currentPage*this.pageSize) < this.users.length) this.currentPage++;
		    },
		    prevPage() {
				if(this.currentPage > 1) this.currentPage--;
		    },
		    setPages(users) {
				let numberOfPages = Math.ceil(users.length / this.perPage);
				this.pagesCount = numberOfPages
				for (let index = 1; index <= numberOfPages; index++) {
					this.pages.push(index);
				}
				this.paginate(users)
		    },
			removeUser(user_id) {
				this.relatedUsers = this.relatedUsers.filter(o => o.id !== user.id)
			},
			getUserById(user_id) {
				const userEdit = this.relatedUsers.filter(o => o.id === user_id)
				return userEdit[0]
			},
			setEditId(id) {
				this.edit_id = id
			},
			activate(el){
				if(el === 2 && this.edit_id === 0) {
					const errorObj = {
						'msg': "Първо трябва да изберете потребител от списъка" ,
	        			'type': 0, 
	        			'active': 1
					}
					this.$store.commit('set_notification', errorObj)
					return
				}

				this.active_el = el
			},
			toProfile(id) {
		    	this.$router.push({ path: `/dashboard/users/${id}` });
		    },
		    toChat(id) {
		    	this.$store.commit('setActiveFriend',id)
		    	this.$router.push({ path: `/dashboard/chat` })
		    },
		},
		created() {
			axios.get(`/api/relatedusers/${this.firm.id}`)
			.then(resp => {
				if(resp.data.hasRel === 1) {
					let filtered =  resp.data.relatedUsers.filter(o => o.id !== this.user.id)
					this.relUsers = filtered.map(obj=> ({ ...obj, canEdit: 1 }))
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
		}

	}
</script>
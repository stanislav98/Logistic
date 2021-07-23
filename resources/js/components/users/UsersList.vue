<template>
	<div class="users_archive">
		<h1>Потребители</h1>
		<div class="card_box filter">
			<div class="card sort_by">
				<input type="text" name="search_user_name" placeholder="Търсене по име" @input="clearPage" v-model="filterUserName">
				<input type="text" name="search_bulstat" placeholder="Търсене по булстат" @input="clearPage" v-model="filterBulstat">
				<input type="text" name="search_name" placeholder="Търсене по име на фирма" @input="clearPage" v-model="filterName">
				<button @click.prevent="clearSearch" class="btn">Изчисти търсенето</button>
			</div>
		</div>
		<div class="list-users">
			<ul class="single-firm">
				<single-user-list
					v-for="user in sortedUsers"
	       			v-bind:key="user.id"
	       			v-bind:user ="user"
				></single-user-list>
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
				    :page-class="'page-item'">
				</paginate>
		</div>
	</div>
</template>
<script>
	import SingleUserList from './SingleUserList.vue';
	export default {
		data() {
			return {
				users: [],
				perPage: 6,
			    page: 1,
			    pages: [],
			    pagesCount: 0,
			    filterName: '',
			    filterBulstat: '',
			    filterUserName: '',
			}
		},
		components: {
		    'single-user-list': SingleUserList
		},
		computed: {
			user() {
				return this.$store.state.user
			},
			activeFriend() {
				return this.$store.state.activeFriend
			},
			sortedUsers() {

	        	let sorted_users = this.users;

	        	if(this.filterUserName) {
	        		sorted_users = sorted_users.filter(i => (String(i.name).toLowerCase().includes(this.filterUserName.toLowerCase())) ? i : '')
	        	}

	        	if(this.filterBulstat) {
	        		sorted_users = sorted_users.filter(i => (String(i.bulstat).toLowerCase().includes(this.filterBulstat.toLowerCase())) ? i : '')
	        	}

	        	if(this.filterName) {
	        		sorted_users = sorted_users.filter(i => (String(i.firmName).toLowerCase().includes(this.filterName.toLowerCase())) ? i : '')
	        	}

		        this.setPages(sorted_users);
	        	sorted_users = this.paginate(sorted_users);

	        	return sorted_users
			}
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
		    clearPage() {
		    	this.page=1
		    },
		    clearSearch() {
		    	this.page = 1
		    	this.filterName = '';
			    this.filterBulstat =  ''
		    },
		},
		created() {
			axios.get('/api/users')
			.then(resp => {
				this.users = resp.data.users.filter((o) => o.id !== this.user.id)
			})
			.catch(err => {
				this.$store.commit('set_notification',err.response.data.notification)
			})
		}
	}
</script>
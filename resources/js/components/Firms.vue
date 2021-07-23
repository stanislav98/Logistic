<template>
	<div class="firms_archive">
		<h1>Фирми</h1>
		<div class="card_box filter">
			<div class="card sort_by">
				<input type="text" name="search_name" placeholder="Търсене по име на фирма" @input="clearPage" v-model="filterName">
				<input type="text" name="search_bulstat" placeholder="Търсене по булстат" @input="clearPage" v-model="filterBulstat">
				<button @click.prevent="clearSearch" class="btn">Изчисти търсенето</button>
			</div>
		</div>
		<div class="list-firms">
			<ul class="single-firm">
				<li class="card_box" v-for="firm in sortedFirms" :key="firm.id">
					<div class="pad_wrapper">
						<div class="header_firm">
							<h4><a :href="`/dashboard/firms/${firm.id}`">{{ firm.name }}</a></h4>
							<button @click.prevent="toProfile(firm.id)" class="btn">Към Профила</button>
						</div class="header_firm">
						<p class="bulstat"><span class="material-icons">business</span><span>Булстат</span> : {{ firm.bulstat }}</p>
						<p class="manager"><span class="material-icons">person</span><span>Мениждър</span> : {{ firm.manager }}</p>
						<p class="address"><span class="material-icons">home</span><span>Адрес</span> : {{ firm.address }}</p>
						<div class="grid_two">
							<ul class="employees">
								<p class="ul_title">Служители</p>
								<li v-for="user in getUserDetails(firm.id)">
									<p class="name">
										<span class="material-icons">person</span>
										<span>Име</span> : {{ user.name }}
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
								<li v-for="vehicle in getVehiclesDetails(firm.id)">
									<p class="vehicle">
										<span class="material-icons">local_shipping</span>
										<span>{{ vehicle.name }} : {{ vehicle.count }}</span>
									</p>							
								</li>
							</ul>
						</div>
					</div>
				</li>
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
	export default {
		data() {
			return {
				firms: [],
				perPage: 6,
			    page: 1,
			    pages: [],
			    pagesCount: 0,
			    filterName: '',
			    filterBulstat: '',
			}
		},
		computed: {
			sortedFirms() {
				//asc

	        	let sorted_firms = this.firms;

	        	if(this.filterBulstat) {
	        		sorted_firms = sorted_firms.filter(i => (String(i.bulstat).toLowerCase().includes(this.filterBulstat)) ? i : '')
	        	}

	        	if(this.filterName) {
	        		sorted_firms = sorted_firms.filter(i => (String(i.name).toLowerCase().includes(this.filterName)) ? i : '')
	        	}

		        this.setPages(sorted_firms);
	        	sorted_firms = this.paginate(sorted_firms);

	        	return sorted_firms
			}
		},
		methods: {
			getUserDetails(id){
				let firm = this.firms.find(o => o.id === id)
				let users = firm.users.split(',')
				let phone = firm.phones.split(',')
				let email = firm.emails.split(',')
				let result = {
					users: []
				}

				for (var i = 0; i < users.length; i++){
					let obj = {
						"name": users[i],
						"phone": phone[i],
						"email": email[i]
					}
					result.users.push(obj)				
				}

		    	return result.users;
			},
			getVehiclesDetails(id){
				let firm = this.firms.find(o => o.id === id)
				let count = firm.vehicle_counts.split(',')
				let vehicles = firm.vehicle_names.split(',')
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
			paginate (firms) {
		      let page = this.page;
		      let perPage = this.perPage;
		      let from = (page * perPage) - perPage;
		      let to = (page * perPage);
		      return firms.slice(from, to);
		    },
		    nextPage() {
		      if((this.currentPage*this.pageSize) < this.firms.length) this.currentPage++;
		    },
		    prevPage() {
		      if(this.currentPage > 1) this.currentPage--;
		    },
		    setPages(firms) {
		      let numberOfPages = Math.ceil(firms.length / this.perPage);
		      this.pagesCount = numberOfPages
		      for (let index = 1; index <= numberOfPages; index++) {
		        this.pages.push(index);
		      }
		      this.paginate(firms)
		    },
		    clearPage() {
		    	this.page=1
		    },
		    clearSearch() {
		    	this.page = 1
		    	this.filterName = '';
			    this.filterBulstat =  ''
		    },
		    toProfile(id) {
		    	this.$router.push({ path: `/dashboard/firms/${id}` });
		    },
		},
		created() {
			axios.get('/api/firms')
			.then(resp => {
				this.firms = resp.data.firms
				this.getUserDetails(resp.data.firms[0].id)
			})
			.catch(err => {
				this.$store.commit('set_notification',error.response.data.notification)
			})
		}
	}
</script>
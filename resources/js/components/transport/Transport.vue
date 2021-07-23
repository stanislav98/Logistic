<template>
	<div class="tovari">
		<google-map v-if="showMap && loaded" :transport="this.transport"></google-map>
		<div class="card_box sort_by">
			<div class="card">
				<div class="row">
					<a class="wrap_icon_text btn"  @click="sort('price')"
					:class="[this.currentSortDir === 'asc' ? 'danger' : '', this.currentSortDir  === 'desc' ? 'green' : '']">
						<span class="material-icons" v-if="this.currentSortDir === 'asc'">arrow_upward</span>
						<span class="material-icons" v-if="this.currentSortDir === 'desc'">arrow_downward</span>
						<span>Цена</span>
					</a>
					<date-picker v-model="sortByDateFrom" type="datetime" placeholder="Изберете дата на транспорт"></date-picker>
					<date-picker v-model="sortByDateTo" type="datetime" placeholder="Изберете дата на разтоварване"></date-picker>
					<p class="wrap_icon_text">
						<span class="material-icons">place</span>
						<v-select :options="fromCities" placeholder="Тръгване от" @input="setFilterFrom"></v-select>
					</p>
					<p class="wrap_icon_text">
						<span class="material-icons">pin_drop</span>
						<v-select :options="toCities" placeholder="Транспорт към" @input="setFilterTo"></v-select>
					</p>
				</div>
				<div class="show_map">
					<a href="#" class="btn" @click="showHideMap">
						<span class="material-icons">map</span>
						<span class="text">{{showMap === false ? 'Показване на картата' : 'Скриване на картата'}}</span>
					</a>
				</div>
			</div>
		</div>
		<div class="list_tovari">
			<transport-single
				v-for="item in sortedTransport"
       			v-bind:key="item.id"
       			v-bind:transport ="item"
				></transport-single>
				<div class="card_box" v-if="sortedTransport.length === 0">
					<div class="card">
						<h1 class="mb-0">Няма намерени товари !</h1>
						<p class="mb-0">Моля изберете нови критерии и опитайте отново</p>
					</div>
				</div>
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
	import SingleTransport from './SingleTransport.vue';
	import GoogleMap from "./GoogleMap.vue";
	import DatePicker from 'vue2-datepicker';
  	import 'vue2-datepicker/index.css';
  	import 'vue2-datepicker/locale/bg';

	export default {
		computed: {
		    // google: gmapApi,
		    newTransport() {
		    	return this.$store.state.newTransport
		    },
	        sortedTransport() {
	        	//asc
	        	let sorted_transport = [];

	        	if(this.currentSortDir ==='asc') {
	        		sorted_transport = this.transport.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
	        	} else {
	        		sorted_transport = this.transport.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
	        	}


	        	if(this.filterFrom) {
	        		sorted_transport = sorted_transport.filter(i => (i.from_city == this.filterFrom) ? i : '')
	        	}
	        	if(this.filterTo) {
	        		sorted_transport = sorted_transport.filter(i => (i.to_city == this.filterTo) ? i : '')
	        	}

        		if(this.sortByDateFrom !== '' && this.sortByDateFrom !== null) {
	        		sorted_transport = sorted_transport.filter(i => (new Date(i.from_date) > new Date(this.sortByDateFrom)) ? i : '')
	        	}

	        	if(this.sortByDateTo !== '' && this.sortByDateTo !== null) {
	        		sorted_transport = sorted_transport.filter(i => (i.to_date == this.sortByDateTo) ? i : '')
	        	}

		        this.setPages(sorted_transport);
	        	sorted_transport = this.paginate(sorted_transport);

	        	return sorted_transport
		    },
		    fromCities() {
		    	return this.transport.map(a => a.from_city).filter((value, index, self) => self.indexOf(value) === index);
		    },
		    toCities() {
		    	return this.transport.map(a => a.to_city).filter((value, index, self) => self.indexOf(value) === index);
		    },
		},
		data() {
			return {
				transport: [],
				filterFrom: '',
				filterTo: '',
			    currentSortDir: 'desc',
			    perPage: 6,
			    page: 1,
			    showMap: true,
			    pages: [],
			    pagesCount:"",
			    loaded: false,
			    sortByDates: [],
			    sortByDateFrom: '',
			    sortByDateTo: '',
	        }
		},
		components: {
			'transport-single': SingleTransport,
			'google-map': GoogleMap,
			'date-picker': DatePicker 
		},
		created() {
		},
	    mounted() {
			if(this.transport.length === 0) {
				axios.get('/api/transport').then((res) => {
					let transport = res.data.transport
					this.transport = transport.map(obj=> ({ ...obj, distance: '0 км', time: '0 часа' }))
					this.loaded = true
				}).catch((error) => {
					this.$store.commit('set_notification',error.response.data.notification)
				})
			}
        },
        watch: {
        	newTransport(newVal,oldVal) {
        		this.appendNewTransport(newVal)
        	}
        },
        methods: {
        	appendNewTransport(trans) {
        		const transport = {...trans, distance: '0 км', time: '0 часа'}
        		this.transport.push(transport)
        	},
		    sort(s) {
		        this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
		    },
		    nextPage() {
		      if((this.currentPage*this.pageSize) < this.transport.length) this.currentPage++;
		    },
		    sortByDate() {
		    },
		    prevPage() {
		      if(this.currentPage > 1) this.currentPage--;
		    },
		    setFilterFrom(value) {
		    	this.filterFrom = value
		    	this.page = 1
		    },
		    setFilterTo(value) {
		    	this.filterTo = value
		    	this.page = 1
		    },
		    showHideMap() {
		    	this.showMap = !this.showMap
		    },
		    setPages(transportSorted) {
		      let numberOfPages = Math.ceil(transportSorted.length / this.perPage);
		      this.pagesCount = numberOfPages
		      for (let index = 1; index <= numberOfPages; index++) {
		        this.pages.push(index);
		      }
		    },
		    paginate(transport) {
		      let page = this.page;
		      let perPage = this.perPage;
		      let from = (page * perPage) - perPage;
		      let to = (page * perPage);
		      return  transport.slice(from, to);
		    }
	    },
	}
</script>
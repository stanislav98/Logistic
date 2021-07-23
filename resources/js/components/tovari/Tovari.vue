<template>
	<div class="tovari">
		<google-map v-if="showMap && loaded" :tovari="this.tovari"></google-map>
		<div class="card_box sort_by">
			<div class="card">
				<div class="row mb-0">
					<a class="wrap_icon_text btn"  @click="sort('price')"
					:class="[this.currentSortDir === 'asc' ? 'danger' : '', this.currentSortDir  === 'desc' ? 'green' : '']">
						<span class="material-icons" v-if="this.currentSortDir === 'asc'">arrow_upward</span>
						<span class="material-icons" v-if="this.currentSortDir === 'desc'">arrow_downward</span>
						<span>Цена</span>
					</a>
					<date-picker v-model="sortByDateFrom" type="datetime" placeholder="Изберете дата на товарене"></date-picker>
					<date-picker v-model="sortByDateTo" type="datetime" placeholder="Изберете дата на разтоварване"></date-picker>
					<p class="wrap_icon_text">
						<span class="material-icons">place</span>
						<v-select :options="fromCities" placeholder="Тръгване от" @input="setFilterFrom"></v-select>
					</p>
					<p class="wrap_icon_text">
						<span class="material-icons">pin_drop</span>
						<v-select :options="toCities" placeholder="Товар към" @input="setFilterTo"></v-select>
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
			<tovari-single
				v-for="item in sortedTovari"
       			v-bind:key="item.id"
       			v-bind:tovar ="item"
				></tovari-single>
				<div class="card_box" v-if="sortedTovari.length === 0">
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
	import SingleTovari from './SingleTovari.vue';
	import GoogleMap from "./GoogleMap.vue";
	import DatePicker from 'vue2-datepicker';
  	import 'vue2-datepicker/index.css';
  	import 'vue2-datepicker/locale/bg';

	export default {
		computed: {
		    // google: gmapApi,
		    newTovar() {
		    	return this.$store.state.newTovar
		    },
	        sortedTovari() {
	        	//asc
	        	let sorted_tovari = [];


	        	if(this.currentSortDir ==='asc') {
	        		sorted_tovari = this.tovari.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
	        	} else {
	        		sorted_tovari = this.tovari.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
	        	}

	        	if(this.filterFrom) {
	        		sorted_tovari = sorted_tovari.filter(i => (i.from_city == this.filterFrom) ? i : '')
	        	}
	        	if(this.filterTo) {
	        		sorted_tovari = sorted_tovari.filter(i => (i.to_city == this.filterTo) ? i : '')
	        	}

	        	if(this.sortByDateFrom !== '' && this.sortByDateFrom !== null) {
	        		sorted_tovari = sorted_tovari.filter(i => (new Date(i.from_date) > new Date(this.sortByDateFrom)) ? i : '')
	        	}

	        	if(this.sortByDateTo !== '' && this.sortByDateTo !== null) {
	        		sorted_tovari = sorted_tovari.filter(i => (i.to_date == this.sortByDateTo) ? i : '')
	        	}

		        this.setPages(sorted_tovari);
	        	sorted_tovari = this.paginate(sorted_tovari);

	        	return sorted_tovari
		    },
		    fromCities() {
		    	return this.tovari.map(a => a.from_city).filter((value, index, self) => self.indexOf(value) === index);
		    },
		    toCities() {
		    	return this.tovari.map(a => a.to_city).filter((value, index, self) => self.indexOf(value) === index);
		    },
		},
		data() {
			return {
				tovari: [],
				filterFrom: '',
				filterTo: '',
			    currentSortDir: 'desc',
			    perPage: 6,
			    page: 1,
			    showMap: true,
			    pages: [],
			    pagesCount:"",
			    loaded: false,
			    sortByDateFrom: '',
			    sortByDateTo: '',
	        }
		},
		components: {
			'tovari-single': SingleTovari,
			'google-map': GoogleMap,
		    'date-picker': DatePicker 
		},
		created() {
		},
	    mounted() {
			if(this.tovari.length === 0) {
				axios.get('/api/tovari').then((res) => {
					let tovari = res.data.tovari
					this.tovari = tovari.map(obj=> ({ ...obj, distance: '0 км', time: '0 часа' }))
					this.loaded = true
				}).catch((error) => {
					this.$store.commit('set_notification',error.response.data.notification)
				})
			}
        },
        watch: {
        	newTovar(newVal,oldVal) {
        		this.appendNewTovar(newVal)
        	}
        },
        methods: {
        	appendNewTovar(tov) {
        		const tovar = {...tov, distance: '0 км', time: '0 часа'}
        		this.tovari.push(tovar)
        	},
		    sort(s) {
		        this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
		    },
		    nextPage() {
		      if((this.currentPage*this.pageSize) < this.tovari.length) this.currentPage++;
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
		    setPages (tovariSorted) {
		      let numberOfPages = Math.ceil(tovariSorted.length / this.perPage);
		      this.pagesCount = numberOfPages
		      for (let index = 1; index <= numberOfPages; index++) {
		        this.pages.push(index);
		      }
		    },
		    paginate (tovari) {
		      let page = this.page;
		      let perPage = this.perPage;
		      let from = (page * perPage) - perPage;
		      let to = (page * perPage);
		      return  tovari.slice(from, to);
		    }
	    },
	}
</script>
<template>
	<div class="tovari">
		<google-map v-if="showMap"></google-map>
		<div class="card_box sort_by">
			<div class="card">
				<div class="row">
					<p class="wrap_icon_text">
						<span class="material-icons" v-if="this.currentSortDir === 'asc'">arrow_upward</span>
						<span class="material-icons" v-if="this.currentSortDir === 'desc'">arrow_downward</span>
						<span @click="sort('price')">Цена</span>
					</p>
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
			<div class="card_box" v-if="sortedTovari.length === 0">
				<div class="card">
					<h1 class="mb-0">Няма намерени товари !</h1>
					<p class="mb-0">Моля изберете нови критерии и опитайте отново</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import SingleTovari from './SingleTovari.vue';
	import GoogleMap from "./GoogleMap.vue";
	export default {
		computed: {
		    tovari () {
		    	return this.$store.state.tovari
		    },
		    // google: gmapApi,
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
				filterFrom: '',
				filterTo: '',
			    currentSortDir: 'desc',
			    perPage: 6,
			    page: 1,
			    showMap: false,
			    pages: [],
			    pagesCount:"",
	        }
		},
		components: {
			'tovari-single': SingleTovari,
			'google-map': GoogleMap,
		},
		created() {
			
		},
	    mounted() {
			if(this.tovari.length === 0) {
				axios.get('/api/tovari').then((res) => {
					const tovari = res.data.tovari
					this.$store.commit('set_tovari',tovari)
				}).catch((error) => {
					console.log(error)
				})
			}
        },
        methods: {
		    sort(s) {
		        this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
		    },
		    nextPage() {
		      if((this.currentPage*this.pageSize) < this.tovari.length) this.currentPage++;
		    },
		    prevPage() {
		      if(this.currentPage > 1) this.currentPage--;
		    },
		    setFilterFrom(value) {
		    	this.filterFrom = value
		    },
		    setFilterTo(value) {
		    	this.filterTo = value
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
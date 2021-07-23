<template>
	<div>
		<ul class="list_actions">
			<li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
				Преглед на товари
			</li>
			<li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}"
				v-if="relatedTovari.length !== 0">
				Редактиране на товар
			</li>
			<li @click.prevent="activate(3)" :class="{ 'btn': active_el === 3, 'btn_second': active_el !== 3}">
				Добавяне на товар
			</li>
		</ul>
		<!-- PREVIEW -->
		<div class="related_tovari" v-if="active_el === 1">
			<h1 class="h1_title mb-12">Вашите Товари</h1>
			<div class="single-firm grid_two my_tovari" v-if="relatedTovari.length !== 0">
					<single-tovar
					v-for="tovar in relatedTovari"
	       			:key="tovar.id"
	       			v-bind:tovar ="tovar"
	       			@clicked="activate"
	       			@edited="setEditId">
	       			</single-tovar>
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
		    :page-class="'page-item'"
		    v-if="relatedTovari.length !== 0">
		</paginate>
			<div v-else>
				<h4>Няма намерени товари !</h4>
			</div>
		</div>
		<!-- Edit tovar -->
		<div class="edit_tovar" v-if="active_el === 2 && edit_id !== 0">
			<edit-tovar :tovar="getTovarById(edit_id)" @clicked="activate"></edit-tovar>
		</div>
		<!-- Add tovar -->
		<div class="add_tovar" v-if="active_el === 3">
			<add-tovar :firmProps="firmProps"></add-tovar>
		</div>
		
	</div>
</template>
<script>
	import SingleTovari from './SingleTovari.vue';
	import EditTovar from './EditTovar.vue';
	import AddTovar from './AddTovar.vue';

	// import Addtovari from './Addtovari.vue';

	export default {
		computed: {
			firm() {
				return this.$store.state.firm
			},
			user() {
				return this.$store.state.user
			},
			relatedTovari() {
				let sorted_tovari = this.relTov;
				
		        this.setPages(sorted_tovari);
	        	sorted_tovari = this.paginate(sorted_tovari);

	        	return sorted_tovari
			}
		},
		data(){
			return {
				relTov: [],
				active_el: 1,
				edit_id: 0,
				perPage: 6,
			    page: 1,
			    pages: [],
			    pagesCount: 0,
			    firmProps: {}
			}
		},
		components: {
		    'edit-tovar': EditTovar,
		    'single-tovar': SingleTovari,
		    'add-tovar': AddTovar
		},
		methods: {
			paginate (tovari) {
		      let page = this.page;
		      let perPage = this.perPage;
		      let from = (page * perPage) - perPage;
		      let to = (page * perPage);
		      return tovari.slice(from, to);
		    },
		    nextPage() {
				if((this.currentPage*this.pageSize) < this.tovari.length) this.currentPage++;
		    },
		    prevPage() {
				if(this.currentPage > 1) this.currentPage--;
		    },
		    setPages(tovari) {
				let numberOfPages = Math.ceil(tovari.length / this.perPage);
				this.pagesCount = numberOfPages
				for (let index = 1; index <= numberOfPages; index++) {
					this.pages.push(index);
				}
				this.paginate(tovari)
		    },
			removeTovar(id) {
				this.relatedTovari = this.relatedTovari.filter(o => o.id !== user.id)
			},
			getTovarById(id) {
				const tovarEdit = this.relatedTovari.filter(o => o.id === id)
				return tovarEdit[0]
			},
			setEditId(id) {
				this.edit_id = id
			},
			activate(el){
				if(el === 2 && this.edit_id === 0) {
					const errorObj = {
						'msg': "Първо трябва да изберете товар от списъка" ,
	        			'type': 0, 
	        			'active': 1
					}
					this.$store.commit('set_notification', errorObj)
					return
				}

				this.active_el = el
			},
			toProfile(id) {
		    	this.$router.push({ path: `/dashboard/tovari/${id}` });
		    },
		    toChat(id) {
		    	this.$store.commit('setActiveFriend',id)
		    	this.$router.push({ path: `/dashboard/chat` })
		    },
		},
		created() {
			axios.get(`/api/relatedtovari/${this.user.id}`)
			.then(resp => {
				let filtered =  resp.data.tovari
				this.relTov = filtered.map(obj=> ({ ...obj, canEdit: 1 }))
				this.firmProps = {
					canEdit: 1,
					rating: this.relTov[0].rating,
					countReports: this.relTov[0].countReports
				}
			})
			.catch(err => {
				if(err.response) {
					this.$store.commit('set_notification',err.response.data.notification)
				} else {
					const errorObj = {
						'msg': "Нещо се обърка ! Моля презаредете страницата" ,
	        			'type': 0, 
	        			'active': 1
					}
					this.$store.commit('set_notification', errorObj)
				}
			})
		}

	}
</script>
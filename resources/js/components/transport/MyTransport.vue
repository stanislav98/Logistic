<template>
	<div>
		<ul class="list_actions">
			<li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
				Преглед на транспорти
			</li>
			<li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}"
				v-if="relatedTransport.length !== 0">
				Редактиране на транспорт
			</li>
			<li @click.prevent="activate(3)" :class="{ 'btn': active_el === 3, 'btn_second': active_el !== 3}">
				Добавяне на транспорт
			</li>
		</ul>
		<!-- PREVIEW -->
		<div class="related_tovari" v-if="active_el === 1">
			<h1 class="h1_title mb-12">Вашият транспорт</h1>
			<div class="single-firm grid_two my_tovari" v-if="relatedTransport.length !== 0">
					<single-transport
					v-for="transport in relatedTransport"
	       			:key="transport.id"
	       			v-bind:transport ="transport"
	       			@clicked="activate"
	       			@edited="setEditId">
	       			</single-transport>
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
		    v-if="relatedTransport.length !== 0">
		</paginate>
			<div v-else>
				<h4>Няма намерени транспорти !</h4>
			</div>
		</div>
		<!-- Edit Transport -->
		<div class="edit_tovar" v-if="active_el === 2 && edit_id !== 0">
			<edit-transport :transport="getTransportById(edit_id)" @clicked="activate"></edit-transport>
		</div>
		<!-- Add Transport -->
		<div class="add_tovar" v-if="active_el === 3">
			<add-transport :firmProps="firmProps"></add-transport>
		</div>
		
	</div>
</template>
<script>
	import SingleTransport from './SingleTransport.vue';
	import EditTransport from './EditTransport.vue';
	import AddTransport from './AddTransport.vue';

	export default {
		computed: {
			firm() {
				return this.$store.state.firm
			},
			user() {
				return this.$store.state.user
			},
			relatedTransport() {
				let sorted_transport = this.relTrans;
				
		        this.setPages(sorted_transport);
	        	sorted_transport = this.paginate(sorted_transport);

	        	return sorted_transport
			}
		},
		data(){
			return {
				relTrans: [],
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
		    'edit-transport': EditTransport,
		    'single-transport': SingleTransport,
		    'add-transport': AddTransport
		},
		methods: {
			paginate (transports) {
		      let page = this.page;
		      let perPage = this.perPage;
		      let from = (page * perPage) - perPage;
		      let to = (page * perPage);
		      return transports.slice(from, to);
		    },
		    nextPage() {
				if((this.currentPage*this.pageSize) < this.transports.length) this.currentPage++;
		    },
		    prevPage() {
				if(this.currentPage > 1) this.currentPage--;
		    },
		    setPages(transports) {
				let numberOfPages = Math.ceil(transports.length / this.perPage);
				this.pagesCount = numberOfPages
				for (let index = 1; index <= numberOfPages; index++) {
					this.pages.push(index);
				}
				this.paginate(transports)
		    },
			removeTransport(id) {
				this.relatedTransport = this.relatedTransport.filter(o => o.id !== user.id)
			},
			getTransportById(id) {
				const transportEdit = this.relatedTransport.filter(o => o.id === id)
				return transportEdit[0]
			},
			setEditId(id) {
				this.edit_id = id
			},
			activate(el){
				if(el === 2 && this.edit_id === 0) {
					const errorObj = {
						'msg': "Първо трябва да изберете транспорт от списъка" ,
	        			'type': 0, 
	        			'active': 1
					}
					this.$store.commit('set_notification', errorObj)
					return
				}

				this.active_el = el
			},
			toProfile(id) {
		    	this.$router.push({ path: `/dashboard/transport/${id}` });
		    },
		    toChat(id) {
		    	this.$store.commit('setActiveFriend',id)
		    	this.$router.push({ path: `/dashboard/chat` })
		    },
		},
		created() {
			axios.get(`/api/relatedtransport/${this.user.id}`)
			.then(resp => {
				let filtered =  resp.data.transport
				this.relTrans = filtered.map(obj=> ({ ...obj, canEdit: 1 }))
				this.firmProps = {
					canEdit: 1,
					rating: this.relTrans[0].rating,
					countReports: this.relTrans[0].countReports
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
<template>
	<article class="single_tovar">
		<button @click.prevent="toDelete(transport.id)" class="btn_second danger">Изтрий</button>
		<div class="heading">
			<div class="left_heading">
				<h3>От {{transport.from_city}} до {{transport.to_city}}</h3>
				<div class="tooltip"  v-if="transport.fast_payment == 1">
					<span class="material-icons">payments</span>
					<span class="tooltiptext">Бързо плащане</span>
				</div>
				<div class="tooltip"   v-if="transport.adr == 1">
					<img :src="'../images/adr.png'" alt="ADR">
					<span class="tooltiptext">ADR - Опасен товар</span>
				</div>
				<div class="row" v-if="transport.rating || transport.countReports">
					<p class="wrap_icon_text" v-if="transport.rating">
						<span class="material-icons yellow">star</span>
						<span>{{ transport.rating + '/5'}}</span>
					</p>
					<p class="wrap_icon_text">
						<span class="material-icons font_danger">error</span>
						<span>{{ transport.countReports + ' Нередности'}}</span>
					</p>
				</div>
			</div>
			<p class="price">{{this.transport.price == '0.00' ? 'Няма посочена цена' : this.transport.price+' лв.' }}</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">business</span>
				<span>{{transport.firmName}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">today</span>
				<span>{{formatDate(transport.from_date)}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">event_busy</span>
				<span>{{formatDate(transport.to_date)}}</span>
			</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">local_shipping</span>
				<span>{{ transport.vehicleName + ' - ' + transport.number_vehicles}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">monitor_weight</span>
				<span>{{ transport.weight == '0.00' ? 'Няма посочено тегло' : transport.weight + " кг."}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">sync_alt</span>
				<span>{{ transport.both_directions ? 'Двупосовечен' : 'Еднопосочен' }}</span>
			</p>
		</div>
		<p v-if="transport.description" class="desc">{{transport.description}}</p>
	</article>
</template>
<script>

export default{
	 	props: {
	 		transport: {
	 			type: Object
	 		},
	 	},
	 	computed: {
	 		user() {
	 			return this.$store.state.user
	 		}
	 	},
		data(){
			return {
				errors:[],
			}
		},
		methods: {
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете транспорта ?")) {
		    		axios.delete(`/api/transport/${id}`)
					.then(resp => {
						let idx = this.$parent.transports.indexOf(this.transport)
						this.$parent.transports.splice(idx,1)
						this.$parent.page = 1
		    			this.$emit('clicked',1)
		    			this.$store.commit('set_notification',resp.data.notification)
					})
					.catch(err => {
						this.$store.commit('set_notification',err.response.data.notification)
					})
		    	}
		    },
			formatDate(strDate) {
				return new Date(strDate).toLocaleString();
			},
		},
		created() {
		}
	}
</script>
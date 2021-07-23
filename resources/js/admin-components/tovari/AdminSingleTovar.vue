<template>
	<article class="single_tovar">
		<button @click.prevent="toDelete(tovar.id)" class="btn_second danger">Изтрий</button>
		<div class="heading">
			<div class="left_heading">
				<h3>От {{tovar.from_city}} до {{tovar.to_city}}</h3>
				<div class="tooltip"  v-if="tovar.fast_payment == 1">
					<span class="material-icons">payments</span>
					<span class="tooltiptext">Бързо плащане</span>
				</div>
				<div class="tooltip"   v-if="tovar.adr == 1">
					<img :src="'../images/adr.png'" alt="ADR">
					<span class="tooltiptext">ADR - Опасен товар</span>
				</div>
				<div class="row" v-if="tovar.rating || tovar.countReports">
					<p class="wrap_icon_text" v-if="tovar.rating">
						<span class="material-icons yellow">star</span>
						<span>{{ tovar.rating + '/5'}}</span>
					</p>
					<p class="wrap_icon_text">
						<span class="material-icons font_danger">error</span>
						<span>{{ tovar.countReports + ' Нередности'}}</span>
					</p>
				</div>
			</div>
			<p class="price">{{this.tovar.price == '0.00' ? 'Няма посочена цена' : this.tovar.price+' лв.' }}</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">business</span>
				<span>{{tovar.firmName}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">today</span>
				<span>{{formatDate(tovar.from_date)}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">event_busy</span>
				<span>{{formatDate(tovar.to_date)}}</span>
			</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">local_shipping</span>
				<span>{{ tovar.vehicleName + ' - ' + tovar.number_vehicles}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">monitor_weight</span>
				<span>{{ tovar.weight == '0.00' ? 'Няма посочено тегло' : tovar.weight + " кг."}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">sync_alt</span>
				<span>{{ tovar.both_directions ? 'Двупосовечен' : 'Еднопосочен' }}</span>
			</p>
		</div>
		<p v-if="tovar.description" class="desc">{{tovar.description}}</p>
	</article>
</template>
<script>

export default{
	 	props: {
	 		tovar: {
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
		    		axios.delete(`/api/tovari/${id}`)
					.then(resp => {
						let idx = this.$parent.tovari.indexOf(this.tovar)
						this.$parent.tovari.splice(idx,1)
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
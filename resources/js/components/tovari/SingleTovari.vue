<template>
	<article class="single_tovar">
		<div class="grid_btns mb-10" v-if="tovar.canEdit == 1">
			<button @click.prevent="toEdit(tovar.id)" class="btn_second">Редактирай</button>
			<button @click.prevent="toDelete(tovar.id)" class="btn_second danger">Изтрий</button>
		</div>
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
			</div>
			<p class="price">{{this.tovar.price == '0.00' ? 'Няма посочена цена' : this.tovar.price+' лв.' }}</p>
		</div>
		<div class="row">
			<a href="#" class="btn message_now" @click.prevent="toChat(tovar.user_id)" v-if="tovar.canEdit !== 1 && tovar.user_id !== this.user.id">
				<span class="material-icons">forward_to_inbox</span>Свържи се сега
			</a>
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
		<div class="row" v-if="this.messageFlag === 1 && this.tovar.canEdit !== 1">
			<p class="wrap_icon_text" >
				<span class="material-icons">add_road</span>
				<span>{{ this.tovar.distance }}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">schedule</span>
				<span>{{ this.tovar.time }}</span>
			</p>
		</div>
		<p v-if="tovar.description" class="desc">{{tovar.description}}</p>
	</article>
</template>
<script>
import {gmapApi} from 'vue2-google-maps';

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
				messageFlag: 1
			}
		},
		methods: {
			toEdit(id) {
		    	this.$emit('edited',id)
		    	this.$emit('clicked', 2)
		    },
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете този товар ?")) {
		    		axios.delete(`/api/tovari/${id}`)
					.then(resp => {
						const idx = this.$parent.relTov.indexOf(this.user)
						this.$parent.relTov.splice(idx, 1)
						this.$parent.page = 1
						this.$store.commit('set_notification',resp.data.notification)
					})
					.catch(err => {
						this.$store.commit('set_notification',err.response.data.notification)
					})
		    	}
		    },
			toChat(id) {
		    	this.$store.commit('setActiveFriend',id)
		    	this.$router.push({ path: `/dashboard/chat` })
		    },
			formatDate(strDate) {
				return new Date(strDate).toLocaleString();
			},
			getLocation() {
			  if (navigator.geolocation) {
			    navigator.geolocation.getCurrentPosition(this.showPosition,this.showError);
			  } else {
			    this.messageFlag = 0
			  }
			},
			showPosition(position) {
			  this.messageFlag = 1
			},
			showError(error) {
			  	this.messageFlag = 0
			},
		    getCoords(lat,lng) {
		      	return {
		      		lat:parseFloat(lat),
		      		lng:parseFloat(lng)
		      	}
		    },
			getDistanceForTovar() {
				if(this.tovar.distance === '0 км' || this.tovar.time === '0 часа') {
					let vm = this
					var service = new google.maps.DistanceMatrixService();
					let start = this.getCoords(this.tovar.to_lat,this.tovar.to_lng)
					let end = this.getCoords(this.tovar.from_lat,this.tovar.from_lng)
					service.getDistanceMatrix({
					    origins: [start],
					    destinations: [end],
					    travelMode: 'DRIVING',
					    unitSystem: google.maps.UnitSystem.metric,
					    avoidHighways: false,
					    avoidTolls: false,
					}, function(response, status) {
			  			if (status == 'OK') {
					  		let obj = response.rows[0].elements[0]
					  		if(obj.distance) {
					  			vm.tovar.distance = obj.distance.text
					  		}
					  		if(obj.duration) {
					  			vm.tovar.time = obj.duration.text
					  		}
						}
					});
				}
			}
		},
		created() {
			this.getLocation();

			setTimeout(() => this.getDistanceForTovar(),3000);
		}
	}
</script>
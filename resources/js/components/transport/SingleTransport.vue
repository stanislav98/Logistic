<template>
	<article class="single_tovar">
		<div class="grid_btns mb-10" v-if="transport.canEdit == 1">
			<button @click.prevent="toEdit(transport.id)" class="btn_second">Редактирай</button>
			<button @click.prevent="toDelete(transport.id)" class="btn_second danger">Изтрий</button>
		</div>
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
			<a href="#" class="btn message_now" @click.prevent="toChat(transport.user_id)" v-if="transport.canEdit !== 1 && transport.user_id !== this.user.id">
				<span class="material-icons">forward_to_inbox</span>Свържи се сега
			</a>
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
		<div class="row" v-if="this.messageFlag === 1 && this.transport.canEdit !== 1">
			<p class="wrap_icon_text" >
				<span class="material-icons">add_road</span>
				<span>{{ this.transport.distance }}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">schedule</span>
				<span>{{ this.transport.time }}</span>
			</p>
		</div>
		<p v-if="transport.description" class="desc">{{transport.description}}</p>
	</article>
</template>
<script>
import {gmapApi} from 'vue2-google-maps';

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
				messageFlag: 1
			}
		},
		methods: {
			toEdit(id) {
		    	this.$emit('edited',id)
		    	this.$emit('clicked', 2)
		    },
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете транспорта ?")) {
		    		axios.delete(`/api/transport/${id}`)
					.then(resp => {
						const idx = this.$parent.relTrans.indexOf(this.transport)
						this.$parent.relTrans.splice(idx, 1)
						this.$parent.page = 1
						this.$emit('edited',0)
		    			this.$emit('clicked',1)
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
			getDistanceForTransport() {
				if(this.transport.distance === '0 км' || this.transport.time === '0 часа') {
					let vm = this
					var service = new google.maps.DistanceMatrixService();
					let start = this.getCoords(this.transport.to_lat,this.transport.to_lng)
					let end = this.getCoords(this.transport.from_lat,this.transport.from_lng)
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
					  			vm.transport.distance = obj.distance.text
					  		}
					  		if(obj.duration) {
					  			vm.transport.time = obj.duration.text
					  		}
						}
					});
				}
			}
		},
		created() {
			this.getLocation();

			setTimeout(() => this.getDistanceForTransport(),3000);
		}
	}
</script>
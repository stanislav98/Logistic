<template>
	<div class="wrap_single_tovar">	
		<div class="single-tovar card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>{{ "Транспорт от " + newTransport.from_city + " до " + newTransport.to_city }}</h1>
					<div class="grid_btns">
						<button @click.prevent="toView()" class="btn_second">Назад</button>
						<button @click.prevent="toDelete(newTransport.id)" class="btn_second danger">Изтрий</button>
					</div>
				</div class="header_tovar">
				<div class="tovar_details">
					<p class="hint">Тръгване от</p>
					<GmapAutocomplete ref="gmapAutocomplete" @place_changed="setFromPlace" :options="autocompleteOptions" placeholder="Something" :placeholder="`${newTransport.from_city}`">
			        </GmapAutocomplete>
			        <p class="hint">Разтоварване в</p>
			        <GmapAutocomplete ref="gmapAutocomplete" @place_changed="setToPlace" :options="autocompleteOptions" :placeholder="`${newTransport.to_city}`">
			        </GmapAutocomplete>
			        <p class="hint">Дата на товарене - Дата на разтоварване</p>
					<date-picker v-model="dates" @change="setTransportDates" type="datetime" range placeholder="Изберете дата на товарене и разтоварване"></date-picker>
					<div class="row">
						<div>
							<input type="checkbox" id="adr" v-model="newTransport.adr">
							<label for="adr">АДР</label>
						</div>
						<div>
							<input type="checkbox" id="fast_payment" v-model="newTransport.fast_payment">
							<label for="fast_payment">Бързо плащане</label>
						</div>
						<div>
							<input type="checkbox" id="both_directions" v-model="newTransport.both_directions">
							<label for="both_directions">Двупосочен</label>
						</div>
					</div>
					<p class="hint">Описание на транспорта</p>
					<textarea class="textarea" name="description" v-model="newTransport.description" />
					<p class="hint">Цена</p>
					<div class="input_holder" :class="{'error' : errors.price }">
						<div class="wrap_elements">
							<span class="material-icons">paid</span>
							<input type="text" name="price" placeholder="Телефонен номер" v-model="newTransport.price">
						</div>
						<span  class="error" v-if="errors.price">{{errors.price[0]}}</span>
					</div>
					<p class="hint">Тегло</p>
					<div class="input_holder" :class="{'error' : errors.weight }">
						<div class="wrap_elements">
							<span class="material-icons">monitor_weight</span>
							<input type="text" name="weight" placeholder="Тегло" v-model="newTransport.weight">
						</div>
						<span class="error" v-if="errors.price">{{errors.weight[0]}}</span>
					</div>
					<p class="hint">Тип на превозното средство</p>
					<v-select label="name" :options="vehicleTypes" placeholder="Тип на транспорта" :value="newTransport.vehicleName" @input="setVehicleType"></v-select>
					<p class="hint">Брой на превозните средства</p>
					<div class="input_holder" :class="{'error' : errors.number_vehicles }">
						<div class="wrap_elements">
							<span class="material-icons">local_shipping</span>
							<input type="text" name="number_vehicles" placeholder="Брой на превозните средства" v-model="newTransport.number_vehicles">
						</div>
						<span class="error" v-if="errors.price">{{errors.number_vehicles[0]}}</span>
					</div>
				</div>

				<div class="center">
					<button class="btn" @click.prevent="updateTransport">Запази промените</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	// import { Carousel, Slide } from 'vue-carousel';
	import DatePicker from 'vue2-datepicker';
  	import 'vue2-datepicker/index.css';
  	import 'vue2-datepicker/locale/bg';

	export default {
		props: {
			transport: {
				type: Object
			}
		},
		data(){
			return {
				errors: [],
				newTransport: {},
				text: 'test',
				dates: [],
				place: '',
				vehicleTypes: [],
				autocompleteOptions: {
			        componentRestrictions: {
			          country: [
			            'bg',
			          ],
			        },
		            fields: ["geometry", "address_components"]
		         },

			}
		},
		components: {
		    'date-picker': DatePicker 
		},
		methods: {
			setTransportDates() {
				this.newTransport.from_date = this.dates[0]
				this.newTransport.to_date = this.dates[1]
			},
			setVehicleType(val){
				this.newTransport.vehicle_type = val.id 
				this.newTransport.vehicleName = val.name
			},
			getDiff(newObj,oldObj) {
				let diff = Object.keys(newObj).reduce((diff, key) => {
				  if (oldObj[key] === newObj[key]) return diff
				  return {...diff, [key]: newObj[key] }
				}, {})

				return diff
			},
			getDates(dates){
				this.dates[0] = new Date(this.transport.from_date)
				this.dates[1] = new Date(this.transport.to_date)
			},
			setToPlace(place) {
				this.newTransport.to_city = place.address_components[0]['long_name']
				this.newTransport.to_lat = place.geometry.location.lat()
				this.newTransport.to_lng = place.geometry.location.lng()
            },
            setFromPlace(place) {
            	this.newTransport.from_city = place.address_components[0]['long_name']
				this.newTransport.from_lat = place.geometry.location.lat()
				this.newTransport.from_lng = place.geometry.location.lng()
            },
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете този транспорт ?")) {
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
		    toView() {
		    	this.$emit('edited',0)
		    	this.$emit('clicked',1)
		    },
		    fetchVehicleTypes() {
		    	axios.get('/api/vehicles')
		    	.then((resp) => {
		    		this.vehicleTypes = resp.data.vehicles
		    	})
		    	.catch((err) => {
		    		this.$store.commit('set_notification',error.response.data.notification)
		    	})
		    },
		    updateTransport() {
		    	this.transport.from_date = this.dates[0]
		    	this.transport.to_date = this.dates[1]

				axios.put(`/api/transport/${this.transport.id}`,this.newTransport).then((response) => {

					Object.assign(this.transport, this.newTransport)
					this.errors = []
					const notification = response.data.notification
			        this.$store.commit('set_notification',notification)
			        
				}).catch((error) => {
					if(error.response.data.notification) {
						this.$store.commit('set_notification',error.response.data.notification)
					} else {
						const errors = error.response.data.errors
						const msg = errors[Object.keys(errors)[0]][0]
						const errorObj = {
							'msg': msg ,
		        			'type': 0, 
		        			'active': 1
						}
						
						this.$store.commit('set_notification', errorObj)
						this.errors = errors;
					}
				})
			},
		},
		created() {
			this.newTransport = { ...this.transport }
			this.getDates()
			this.fetchVehicleTypes()
		}

	}
</script>
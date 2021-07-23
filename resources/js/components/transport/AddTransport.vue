<template>
	<div class="wrap_single_tovar">	
		<div class="single-tovar card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>Добавяне на нов транспорт</h1>
					<button @click.prevent="toView()" class="btn_second">Назад</button>
				</div class="header_tovar">
				<div class="tovar_details">
					<p class="hint">Тръгване от</p>
					<GmapAutocomplete ref="gmapAutocomplete" @place_changed="setFromPlace" :options="autocompleteOptions" placeholder="Something" :placeholder="`${newTrans.from_city}`">
			        </GmapAutocomplete>
			        <p class="hint">Разтоварване в</p>
			        <GmapAutocomplete ref="gmapAutocomplete" @place_changed="setToPlace" :options="autocompleteOptions" :placeholder="`${newTrans.to_city}`">
			        </GmapAutocomplete>
			        <p class="hint">Дата на товарене - Дата на разтоварване</p>
					<date-picker v-model="dates" @change="setTransportDates" type="datetime" range placeholder="Изберете дата на товарене и разтоварване"></date-picker>
					<div class="row">
						<div>
							<input type="checkbox" id="adr" v-model="newTrans.adr">
							<label for="adr">АДР</label>
						</div>
						<div>
							<input type="checkbox" id="fast_payment" v-model="newTrans.fast_payment">
							<label for="fast_payment">Бързо плащане</label>
						</div>
						<div>
							<input type="checkbox" id="both_directions" v-model="newTrans.both_directions">
							<label for="both_directions">Двупосочен</label>
						</div>
					</div>
					<p class="hint">Описание на транспорта</p>
					<textarea class="textarea" name="description" v-model="newTrans.description" />
					<p class="hint">Цена</p>
					<div class="input_holder" :class="{'error' : errors.price }">
						<div class="wrap_elements">
							<span class="material-icons">paid</span>
							<input type="text" name="price" placeholder="Телефонен номер" v-model="newTrans.price">
						</div>
						<span  class="error" v-if="errors.price">{{errors.price[0]}}</span>
					</div>
					<p class="hint">Тегло</p>
					<div class="input_holder" :class="{'error' : errors.weight }">
						<div class="wrap_elements">
							<span class="material-icons">monitor_weight</span>
							<input type="text" name="weight" placeholder="Тегло" v-model="newTrans.weight">
						</div>
						<span class="error" v-if="errors.price">{{errors.weight[0]}}</span>
					</div>
					<p class="hint">Тип на превозното средство</p>
					<v-select label="name" :options="vehicleTypes" placeholder="Тип на транспорта" :value="newTrans.vehicleName" @input="setVehicleType"></v-select>
					<p class="hint">Брой на превозните средства</p>
					<div class="input_holder" :class="{'error' : errors.number_vehicles }">
						<div class="wrap_elements">
							<span class="material-icons">local_shipping</span>
							<input type="text" name="number_vehicles" placeholder="Бройка на камионите" v-model="newTrans.number_vehicles">
						</div>
						<span class="error" v-if="errors.price">{{errors.number_vehicles[0]}}</span>
					</div>
				</div>

				<div class="center">
					<button class="btn" @click.prevent="addTransport">Запази промените</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import DatePicker from 'vue2-datepicker';
  	import 'vue2-datepicker/index.css';
  	import 'vue2-datepicker/locale/bg';
 
	export default {
		computed: {
			user() {
				return this.$store.state.user
			},
			firm() {
				return this.$store.state.firm
			}
		},
		props: {
			firmProps: {
				type: Object
			}
		},
		data(){
			return {
				errors: [],
				text: 'test',
				dates: [],
				place: '',
				vehicleTypes: [],
				newTrans: {},
				autocompleteOptions: {
			        componentRestrictions: {
			          country: [
			            'bg',
			          ],
			        },
		            fields: ["geometry", "formatted_address"]
		         },

			}
		},
		components: {
		    'date-picker': DatePicker 
		},
		methods: {
			initEmptyTrans() {
				this.newTrans = {
					from_city: '',
					to_city: '',
					from_lat: '',
					to_lat: '',
					from_lng: '',
					to_lng: '',
					number_vehicles: 1,
					weight: 0,
					price: 0,
					adr: 0,
					fast_payment: 0,
					both_directions: 0,
					description: '',
					type: 0
				}
			},
			setToPlace(place) {
				this.newTrans.to_city = place.formatted_address
				this.newTrans.to_lat = place.geometry.location.lat()
				this.newTrans.to_lng = place.geometry.location.lng()
            },
            setVehicleType(val){
				this.newTrans.vehicle_type = val.id 
				this.newTrans.vehicleName = val.name
			},
            setFromPlace(place) {
            	this.newTrans.from_city = place.formatted_address
				this.newTrans.from_lat = place.geometry.location.lat()
				this.newTrans.from_lng = place.geometry.location.lng()
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
		    setTransportDates() {
				this.newTrans.from_date = this.dates[0]
				this.newTrans.to_date = this.dates[1]
			},
		    addTransport() {
		    	this.newTrans.user_id = this.user.id
		    	this.newTrans.firm_id = this.user.company_id

				axios.post(`/api/transport/`,this.newTrans).then((response) => {
					Object.assign(this.newTrans, this.firmProps)
					this.errors = []
					this.$parent.relTrans.push(this.newTrans)
					this.initEmptyTrans()
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
			this.initEmptyTrans()
			this.fetchVehicleTypes()
		}

	}
</script>
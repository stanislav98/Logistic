<template>
	<div class="wrap_single_tovar">	
		<div class="single-tovar card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>Добавяне на нов товар</h1>
					<button @click.prevent="toView()" class="btn_second">Назад</button>
				</div class="header_tovar">
				<div class="tovar_details">
					<p class="hint">Тръгване от</p>
					<GmapAutocomplete ref="gmapAutocomplete" @place_changed="setFromPlace" :options="autocompleteOptions" placeholder="Something" :placeholder="`${newTovar.from_city}`">
			        </GmapAutocomplete>
			        <p class="hint">Разтоварване в</p>
			        <GmapAutocomplete ref="gmapAutocomplete" @place_changed="setToPlace" :options="autocompleteOptions" :placeholder="`${newTovar.to_city}`">
			        </GmapAutocomplete>
			        <p class="hint">Дата на товарене - Дата на разтоварване</p>
					<date-picker v-model="dates" @change="setTovarDates" type="datetime" range placeholder="Изберете дата на товарене и разтоварване"></date-picker>
					<div class="row">
						<div>
							<input type="checkbox" id="adr" v-model="newTovar.adr">
							<label for="adr">АДР</label>
						</div>
						<div>
							<input type="checkbox" id="fast_payment" v-model="newTovar.fast_payment">
							<label for="fast_payment">Бързо плащане</label>
						</div>
						<div>
							<input type="checkbox" id="both_directions" v-model="newTovar.both_directions">
							<label for="both_directions">Двупосочен</label>
						</div>
					</div>
					<p class="hint">Описание на товара</p>
					<textarea class="textarea" name="description" v-model="newTovar.description" />
					<p class="hint">Цена</p>
					<div class="input_holder" :class="{'error' : errors.price }">
						<div class="wrap_elements">
							<span class="material-icons">paid</span>
							<input type="text" name="price" placeholder="Телефонен номер" v-model="newTovar.price">
						</div>
						<span  class="error" v-if="errors.price">{{errors.price[0]}}</span>
					</div>
					<p class="hint">Тегло</p>
					<div class="input_holder" :class="{'error' : errors.weight }">
						<div class="wrap_elements">
							<span class="material-icons">monitor_weight</span>
							<input type="text" name="weight" placeholder="Тегло" v-model="newTovar.weight">
						</div>
						<span class="error" v-if="errors.price">{{errors.weight[0]}}</span>
					</div>
					<p class="hint">Тип на превозното средство</p>
					<v-select label="name" :options="vehicleTypes" placeholder="Тип на товара" :value="newTovar.vehicleName" @input="setVehicleType"></v-select>
					<p class="hint">Брой на нужните превозни средства</p>
					<div class="input_holder" :class="{'error' : errors.number_vehicles }">
						<div class="wrap_elements">
							<span class="material-icons">local_shipping</span>
							<input type="text" name="number_vehicles" placeholder="Бройка на нужните камиони" v-model="newTovar.number_vehicles">
						</div>
						<span class="error" v-if="errors.price">{{errors.number_vehicles[0]}}</span>
					</div>
				</div>

				<div class="center">
					<button class="btn" @click.prevent="addTovar">Запази промените</button>
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
	// import SingleTovarList from './SingleTovarList.vue';

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
				newTovar: {
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
					type: 1
				},
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
		    // Carousel,
		    // Slide,
		    'date-picker': DatePicker 
		    // 'single-tovar-list': SingletovarList
		},
		methods: {
			setToPlace(place) {
				this.newTovar.to_city = place.formatted_address
				this.newTovar.to_lat = place.geometry.location.lat()
				this.newTovar.to_lng = place.geometry.location.lng()
            },
            setVehicleType(val){
				this.newTovar.vehicle_type = val.id 
				this.newTovar.vehicleName = val.name
			},
            setFromPlace(place) {
            	this.newTovar.from_city = place.formatted_address
				this.newTovar.from_lat = place.geometry.location.lat()
				this.newTovar.from_lng = place.geometry.location.lng()
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
		    setTovarDates() {
				this.newTovar.from_date = this.dates[0]
				this.newTovar.to_date = this.dates[1]
			},
		    addTovar() {
		    	this.newTovar.user_id = this.user.id
		    	this.newTovar.firm_id = this.user.company_id

				axios.post(`/api/tovari/`,this.newTovar).then((response) => {
					Object.assign(this.newTovar, this.firmProps)
					this.errors = []
					this.$parent.relTov.push(this.newTovar)
					this.newTovar = {
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
						type: 1
					}
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
			this.fetchVehicleTypes()
		}

	}
</script>
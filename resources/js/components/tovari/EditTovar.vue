<template>
	<div class="wrap_single_tovar">	
		<div class="single-tovar card_box">
			<div class="pad_wrapper">
				<div class="header_firm div_underlined">
					<h1>{{ "Товар от " + newTovar.from_city + " до " + newTovar.to_city }}</h1>
					<div class="grid_btns">
						<button @click.prevent="toView()" class="btn_second">Назад</button>
						<button @click.prevent="toDelete(newTovar.id)" class="btn_second danger">Изтрий</button>
					</div>
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
					<button class="btn" @click.prevent="updateTovar">Запази промените</button>
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
		props: {
			tovar: {
				type: Object
			}
		},
		data(){
			return {
				errors: [],
				newTovar: {},
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
		    // Carousel,
		    // Slide,
		    'date-picker': DatePicker 
		    // 'single-tovar-list': SingletovarList
		},
		methods: {
			setTovarDates() {
				this.newTovar.from_date = this.dates[0]
				this.newTovar.to_date = this.dates[1]
			},
			setVehicleType(val){
				this.newTovar.vehicle_type = val.id 
				this.newTovar.vehicleName = val.name
			},
			getDiff(newObj,oldObj) {
				let diff = Object.keys(newObj).reduce((diff, key) => {
				  if (oldObj[key] === newObj[key]) return diff
				  return {...diff, [key]: newObj[key] }
				}, {})

				return diff
			},
			getDates(dates){
				this.dates[0] = new Date(this.tovar.from_date)
				this.dates[1] = new Date(this.tovar.to_date)
			},
			setToPlace(place) {
				this.newTovar.to_city = place.address_components[0]['long_name']
				this.newTovar.to_lat = place.geometry.location.lat()
				this.newTovar.to_lng = place.geometry.location.lng()
            },
            setFromPlace(place) {
            	this.newTovar.from_city = place.address_components[0]['long_name']
				this.newTovar.from_lat = place.geometry.location.lat()
				this.newTovar.from_lng = place.geometry.location.lng()
            },
		    toDelete(id) {
		    	if(confirm("Сигурни ли сте че искате да изтриете този потребител ?")) {
		    		axios.delete(`/api/tovari/${id}`)
					.then(resp => {
						const idx = this.$parent.relTov.indexOf(this.tovar)
						this.$parent.relTov.splice(idx, 1)
						this.$parent.page = 1
						this.$emit('edited',0)
		    			this.$emit('clicked',1)
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
		    updateTovar() {
		    	this.tovar.from_date = this.dates[0]
		    	this.tovar.to_date = this.dates[1]
		    	// let diff = this.getDiff(this.newTovar,this.tovar)

				axios.put(`/api/tovari/${this.tovar.id}`,this.newTovar).then((response) => {

					Object.assign(this.tovar, this.newTovar)
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
			this.newTovar = { ...this.tovar }
			this.getDates()
			this.fetchVehicleTypes()
		}

	}
</script>
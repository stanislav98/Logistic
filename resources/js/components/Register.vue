<template>
	<div class="login">
		<div class="left">
			<img :src="'../images/register_truck.svg'">
		</div>
		<div class="right">
			<div class="topbar">
				<div class="container">
					<router-link to="/"><img :src="'../images/logo_main.svg'"></router-link>
					<div class="right">
						<router-link to="/login" class="btn">Вход</router-link>
					</div>
			</div>
			</div>
			<h1 class="center">Регистрация</h1>
			<div class="container_steps">
		      <ul class="progressbar">
		          <li :class="{ 'active' : form.step >= 0}">Информация</li>
		          <li :class="{ 'active' : form.step >= 1}">Автопарк</li>
		          <li :class="{ 'active' : form.step >= 2}">Плащане</li>
			  </ul>
			</div>
			<div class="form_wrapper" v-if="form.step == 0">
				<input type="hidden" name="form.step" value="0">
				<div class="input_holder" :class="{'error' : errors.bulstat }">
					<div class="wrap_elements">
						<span class="material-icons">business</span>
						<input type="text" name="bulstat" placeholder="Булстат" v-model="form.bulstat">
					</div>
					<span  class="error" v-if="errors.bulstat">{{errors.bulstat[0]}}</span>
				</div>
				<div class="input_holder" :class="{'error' : errors.name }">
					<div class="wrap_elements">
						<span class="material-icons">person</span>
						<input type="text" name="name" placeholder="Име" v-model="form.name">
					</div>
					<span  class="error" v-if="errors.name">{{errors.name[0]}}</span>
				</div>
				<div class="input_holder" :class="{'error' : errors.phone_number }">
					<div class="wrap_elements">
						<span class="material-icons">phone</span>
						<input type="text" name="phone_number" placeholder="Телефонен номер" v-model="form.phone_number">
					</div>
					<span  class="error" v-if="errors.phone_number">{{errors.phone_number[0]}}</span>
				</div>
				<div class="input_holder" :class="{'error' : errors.password }">
					<div class="wrap_elements">
						<span class="material-icons">https</span>
						<input type="password" name="password" placeholder="Парола" v-model="form.password">
					</div>
					<span class="error" v-if="errors.password">{{errors.password[0]}}</span>
				</div>
				<div class="input_holder" :class="{'error' : errors.password_confirmation }">
					<div class="wrap_elements">
						<span class="material-icons">https</span>
						<input type="password" name="password_confirmation" placeholder="Повторете паролата" v-model="form.password_confirmation" 
						:class="{'error' : errors.password_confirmation }">
					</div>
					<span class="error" v-if="errors.password_confirmation">{{errors.password_confirmation[0]}}</span>
				</div>
				<div class="input_holder" :class="{'error' : errors.email }">
					<div class="wrap_elements">
						<span class="material-icons">email</span>
						<input type="email" name="email" placeholder="Имейл" v-model="form.email">
					</div>
					<span class="error" v-if="errors.email">{{errors.email[0]}}</span>
				</div>
			</div>

			<div class="form_wrapper" v-if="form.step == 1">
				<input type="hidden" name="form.step" value="1">
				<div class="top_err center">
					<span class="error" v-if="errors.vehicles">{{errors['vehicles']}}</span>
				</div>
				<vehicle-form
				v-for="item in form.vehicles"
       			v-bind:key="item.id"
       			v-bind:vehicle ="item"
				></vehicle-form>
			</div>
			<div class="form_wrapper center" v-if="form.step == 2">
				<p>В рамките на 24 часа Вие ще имате възможността да разгледате, някои от основните функции на сайта.</p>
				<p>Ако не извършите плащане в този диапазон функциите, който ще можете да използвате ще бъдат ограничени,</p> 
				<p>докато не заплатите абонамета си. Благодарим ви че избрахте да станете част от нашия екип.</p> 
				<button @click.prevent="saveForm" type="submit" class="btn_second" v-if="form.step == 2">Регистрация</button>
			</div>
			<div class="step_wrapper">
				<a href="#"  @click.prevent="nextStep" class="btn_second next-step"  v-if="form.step < 2">Следваща стъпка</a>
			</div>
		</div>
	</div>
</template>
<script>
	import VehiclesForm from './VehiclesForm.vue';
	export default{
		data(){
			return {
				form: {
					name: '',
					email: '',
					bulstat: '',
					phone_number: '',
					password: '',
					password_confirmation: '',
					step: 0,
					vehicles: []
				},
				errors:[],
			}
		},
		components: {
			'vehicle-form': VehiclesForm,
		},
		methods: {
			saveForm(){
				axios.post('/api/register',this.form).then((response) => {
					this.$router.push({ name:"Login" });
				}).catch((error) => {
					this.errors = error.response.data.errors;
				})
			},
			nextStep() {
				axios.post('/api/nextstep',this.form).then(() => {
					this.form.step +=1;
				}).catch((error) => {
					 this.errors = error.response.data.errors;
					 console.log(this.errors);
				})
			},
			beforeStep() {
				this.form.step -=1;
			},
		},
		created() {
			axios.post('api/vehicles').then((res) => {
				let vehicles = res.data.vehicles;
				vehicles.forEach(function(i) {
					i.count = 0;
					i.disabled = i.id == 1 ? 1 : 0
				});
				this.form.vehicles = res.data.vehicles;
				console.log(this.form.vehicles);
			}).catch((error) => {
						console.log(error)
			})
		}
	}
</script>
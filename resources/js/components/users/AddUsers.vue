<template>
	<form enctype="multipart/form-data">
		<div class="message">
			<p class="mb-8">За да добавите нов потребител трябва да качите файл със валиден договор</p>
			<p class="mb-8">След като информацията е разгледана от администратора</p>
			<p class="mb-8">Потребителят ще бъде добавен при условие , че има валиден договор към фирмата ви</p>
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
		<input type="file" name="file" class="mb-24" @change="editPhoto($event)">
		<div class="center mb-24">
			<button @click="saveForm" type="submit" class="btn">Добавяне</button>
		</div>
	</form>
</template>
<script>
	export default {
		computed: {
			user() {
				return this.$store.state.user
			},
			firm() {
				return this.$store.state.firm
			}
		},
		data() {
			return {
				form: {
					name: '',
					email: '',
					phone_number: '',
					password: '',
					password_confirmation: '',
					file: []
				},
				errors:[],
			}
		},
		methods: {
			 editPhoto(event,reportId) {
	        	this.form.file = event.target.files[0]
	        },
			saveForm(e) {
				e.preventDefault();

				const config = {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }

                let data = new FormData();
            	data.append('file', this.form.file);
            	data.append('name', this.form.name);
            	data.append('email', this.form.email);
            	data.append('phone_number', this.form.phone_number);
            	data.append('password', this.form.password);
            	data.append('password_confirmation', this.form.password_confirmation);
            	data.append('company_id', this.user.company_id);
            	data.append('has_payed', 1);
            	data.append('role_id', 5);

				axios.post(`/api/add-users`, data, config)
				.then(resp => {
					this.form = {
						name: '',
						email: '',
						phone_number: '',
						password: '',
						password_confirmation: '',
						file: []
					}
					let user = resp.data.user
					user.canEdit = 1;
					user.bulstat = this.firm.bulstat
					user.roleName = "Служител"
					user.firmId = this.firm.id
					user.firmName = this.firm.name
					this.errors = []
					this.$parent.relUsers.push(user)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					let errors = [];
					if(err.response.data.errors) {
						errors = err.response.data.errors
						this.errors = errors
					}
					if(err.response.data.notification) {
						this.$store.commit('set_notification',err.response.data.notification)
					} else {
						const msg = errors[Object.keys(errors)[0]][0]
						const errorObj = {
							'msg': msg ,
		        			'type': 0, 
		        			'active': 1
						}
						this.$store.commit('set_notification', errorObj)
					}
				})
			}
		}
	}
</script>
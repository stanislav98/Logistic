<template>
	<div class="report_wrapper">
		<div class="heading_single_component">
			<h4 class="sub_title">Нарушения</h4>
			<button @click.prevent="reportState == 1 ? reportState = 0 : reportState = 1" class="btn_second">{{text[reportState] }}</button>
		</div>
		<!-- Add report -->
		<div class="card_box add_comment" v-if="reportState === 1">
			<div class="wrapper">
				<form @submit="addReport" enctype="multipart/form-data">
					<input type="text" name="reason" v-model="reportReason" placeholder="Причина за докладване">
					<textarea type="text" name="body" v-model="reportBody" placeholder="Въведете вашето обяснение" />
					<input type="file" @change="selectFile">
					<button class="btn">Докладване</button>
				</form>
			</div>
		</div>
		<!-- End Add report -->

		<!-- View reports -->
		<ul class="comments-list card_box">
			<li class="single-comment div_underlined" v-for="report in reports" :key="report.id" v-if="">
				<div class="comment_heading">
					<a :href="`/dashboard/user/${report.user_id}`" class="user">
						<span class="material-icons">person</span>
						{{ getReportAuthorName(report.user_id)  }}
					</a>
					<div v-if="report.user_id === user.id">
						<span class="material-icons edit" @click="editReport(report.id)">edit</span>
						<span class="material-icons delete" @click="deleteReport(report.id)">delete</span>
					</div>
				</div>
				<p class="verifier" v-bind:class="report.active === true ? 'verified' : 'not_verified'">
					<span class="material-icons">verified_user</span>
					<span>{{ report.active === true ? 'Потвърдено': 'Не е потвърдено' }} от администратор</span>
				</p>
				<p class="date" v-if="report.created_at">
					<span class="material-icons">today</span>
					<span class="date_added">Дата на публикуване : {{ formatDate(report.created_at) }} </span>
				</p>
				<div class="report_body" v-if="report.inEdit === 0">
					<p class="reason">Причина : {{ report.reason }}</p>
					<span>{{ report.body }}</span>
					<a :href="`${report.path}`" target="_blank" class="img_wrapper" v-if="report.path && report.toDeleteImage !== 1">
						<img :src="`${report.path}`" :alt="`${report.reason}`">
					</a>
				</div>
				<!-- EDIT REPORT -->
				<div class="edit" v-if="report.inEdit === 1">
					<form @submit="edit($event,report.id)" enctype="multipart/form-data">
						<input type="text" name="report_reason" id="report_reason" v-model="report.reason" />
						<textarea name="edit_report" id="edit_report" v-model="report.body" />
						<div class="img_del_upl" v-if="report.path && report.toDeleteImage !== 1">
							<img :src="`${report.path}`" :alt="`${report.reason}`">
							<span class="material-icons delete_img" @click="report.toDeleteImage = 1">delete</span>
						</div>
						<div class="new_file">
							<p class="upl_text">
								{{ report.path == null ? 'Качване на снимка' : 'Ако желаете да смените снимката може да качите нова' }}
							</p>
							<input type="file" @change="editPhoto($event,report.id)">
						</div>
						<div class="btns_wrapper">
							<button @click="editReport(report.id)" class="btn_second">Изход от редакция</button>
							<button class="btn_second">Редактирай</button>
						</div>
					</form>
				</div>
				<!-- /EDIT REPORT -->
			</li>
		</ul>
		<!-- /View report -->
	


	</div>
</template>
<script>
	export default {
		computed: {
			onlineUsers() {
				return this.$store.state.onlineUsers
			},
			user() {
				return this.$store.state.user
			},
		},
		data() {
			return {
				photo: '',
				reportBody: '',
				reportReason: '',
				reports: [],
				reportState: 0,
				text: ['Докладване','Скриване']
			}
		},
		methods: {
			selectFile(event) {
	            this.photo = event.target.files[0];
	        },
	        editPhoto(event,reportId) {
	        	let index = this.reports.find(o => o.id === reportId)
	        	if(index.path && index.name) {
	        		index.toDeleteImage = 1
	        	}
	        	index.newImageUpload = event.target.files[0]
	        },
			editReport(reportId) {
				let index = this.reports.find(o => o.id === reportId)
				index.toDeleteImage = 0
				index.inEdit === 0 ? index.inEdit = 1 : index.inEdit = 0
			},
			edit(e,reportId) {
				e.preventDefault();
				let index = this.reports.find(o => o.id === reportId)
				let key = this.reports.findIndex(o => o.id === reportId)
				const config = {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }

                let data = new FormData();
            	data.append('file', index.newImageUpload);
                data.append('toDeleteImage', index.toDeleteImage);
                data.append('reason', index.reason);
                data.append('body', index.body);
                data.append('user_id', this.user.id);
                data.append('firm_id', this.$route.params.id);

				axios.post(`/api/reports/update/${reportId}`, data, config)
				.then(resp => {
					let editedReport = resp.data.report;
					editedReport.inEdit = 0
					editedReport.toDeleteImage = 0
					editedReport.newImageUpload = ''
					Object.assign(index,editedReport)
					console.log(index)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					if(err.response.data.notification) {
						this.$store.commit('set_notification',err.response.data.notification)
					} else {
						const errors = err.response.data.errors
						const msg = errors[Object.keys(errors)[0]][0]
						const errorObj = {
							'msg': msg ,
		        			'type': 0, 
		        			'active': 1
						}
						this.$store.commit('set_notification', errorObj)
					}
				})
			},
			deleteReport(reportId) {
				axios.delete(`/api/reports/${reportId}`)
				.then(resp => {
					//filter e.g remove the report
					this.reports = this.reports.filter(e => e.id !== reportId)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			getReportAuthorName(authorId) {
				if(authorId === this.user.id) {
					return this.user.name
				}
				const author = this.onlineUsers.find((e) => e.id === authorId)
				return author.name
			},
			fetchReports() {
				axios.get(`/api/reports/${this.$route.params.id}`)
				.then(resp => {
					let reports = resp.data.reports
					reports = reports.map(obj=> ({ ...obj, inEdit: 0, toDeleteImage: 0, newImageUpload: '' }))
					this.reports = reports
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			formatDate(strDate) {
				return new Date(strDate).toLocaleString();
			},
			addReport(e) {
				e.preventDefault();
                const config = {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }

                let data = new FormData();
                data.append('file', this.photo);
                data.append('reason', this.reportReason);
                data.append('body', this.reportBody);
                data.append('user_id', this.user.id);
                data.append('firm_id', this.$route.params.id);

				axios.post('/api/reports/', data, config)
				.then(resp => {
					const report = resp.data.report
					report.inEdit = 0
					report.toDeleteImage = 0
					report.newImageUpload = ''
					this.reportReason = ''
					this.reportBody = ''
					this.reportPhoto = ''
					this.reports.unshift(report)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					if(err.response.data.notification) {
						this.$store.commit('set_notification',err.response.data.notification)
					} else {
						const errors = err.response.data.errors
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
		},
		created() {
			this.fetchReports()
		}
	}
</script>
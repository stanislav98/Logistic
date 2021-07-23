<template>
	<div class="report_wrap div_underlined">
		<h1>{{ this.firm.name }}</h1>
		<div class="firm_details">
        	<p class="bulstat"><span class="material-icons">business</span><span>Булстат</span> : {{ this.firm.bulstat }}</p>
		</div>
		<ul class="list_reports" v-if="this.notActiveReports.length > 0">
			<li 
				v-for="report in notActiveReports"
				v-bind:key="report.id"
				v-bind:report ="report"
				class="mtb-10"
				v-if=""
			>
				{{ setReportedIndex(report.user_id) }} 
				<div class="grid_two reports" v-if="loaded === true">
					<div>
						<p>Причина : {{ report.reason }}</p>
						<p>Описание : {{ report.body }}</p>
						<p>Създадено на : {{ new Date(report.created_at).toLocaleString() }}</p>
						<p>Добавено от потребител : {{ getReporterName() }} </p> 
						<p>Име на фирма : {{ getReporterFirmName() }} </p> 
					</div>
					<a :href="`${report.path}`" class="img_wrapper_report">
						<img :src="`${report.path}`" :alt="`${report.name}`" target="_blank">
					</a>
					<div class="btn_wrapper">
						<button class="btn small" @click="removeReport(report.id)">Премахни докладването</button>
						<button class="btn_second small" @click="acceptReport(report.id)">Одобри докладването</button>
					</div>
				</div>
			</li>
		</ul>
		<div v-else>
			<h3>Няма намерени докладвания !</h3>
		</div>
	</div>
</template>
<script>
	export default {
		props: {
			firm: {
				type: Object
			}
		},
		computed: {
			notActiveReports() {
				return this.reports.filter(o => o.active === false && o.name !== null && o.path !== null)
			},
			onlineUsers() {
				return this.$store.state.onlineUsers
			}
		},
		data() {
			return {
				reports: [],
				idx: '',
				loaded: true,
			}
		},
		methods: {
			fetchReport() {
				axios.get(`/api/reports/${this.firm.id}`)
				.then(resp => {
					const reports = resp.data.reports
					this.reports = reports
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			setReportedIndex(id) {
				this.loaded = false
				this.idx = this.onlineUsers.findIndex(o => o.id = id)
				if(this.idx !== '' && this.idx !== null && this.idx !== undefined) {
					this.loaded = true
				}
			},
			getReporterName() {
				return this.onlineUsers[this.idx].name
			},
			getReporterFirmName(id) {
				return this.onlineUsers[this.idx].firmName
			},
			acceptReport(id) {
				axios.put(`/api/reports/${id}`)
				.then(resp => {
					let idx = this.reports.findIndex(o => o.id === id)
					this.reports.splice(idx,1)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			removeReport(id) {
				axios.delete(`/api/reports/${id}`)
				.then(resp => {
					let idx = this.reports.findIndex(o => o.id === id)
					this.reports.splice(idx,1)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			}
		},
		created() {
			this.fetchReport()
		}
	}
</script>
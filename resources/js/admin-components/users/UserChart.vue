<template>
	<div>
		<div class="chart-wrap">
			<div class="filters_wrap">
				<date-picker v-model="filterDate" @change="filterChart" type="date" placeholder="Регистрирани след"></date-picker>
			</div>
		    <div id="chart">
				<vue-apex-charts type="pie" width="700" :options="chartOptions" :series="series" ref="realtimeChart"></vue-apex-charts>
			</div>
		</div>
	</div>
</template>
<script>
	import VueApexCharts from 'vue-apexcharts';
	import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import 'vue2-datepicker/locale/bg';

	export default {
        components: {
          VueApexCharts,
          DatePicker
        },
        props: {
        	users: {
        		type:Array
        	},
        	unAuthorizedUsers: {
        		type:Array
        	}
    	},
        data() {
        	return {
				series: [],
				loaded: false,
				filterDate: '',
				chartOptions: {
					labels: [],
					dataLabels: {
						enabled: true
					},
					responsive: [{
						breakpoint: 480,
						options: {
							chart: {
								width: 200
							},
							legend: {
								show: false
							}
						}
					}],
					legend: {
						position: 'right',
						offsetY: 0,
						height: 230,
					}
				},

        	}
		},
		mounted() {
			this.filterChart(null);
		},
		watch: {
			unAuthorizedUsers(val) {
				if(val !== undefined) {
					this.chartOptions.labels.push("Неодобрени потребители")
					this.$refs.realtimeChart.updateSeries(this.series, false, true);
				}
			},
			users(val) {
				if(val !== undefined) {
					this.series = []
					this.series.push(val.length)
					this.chartOptions.labels.push("Одобрени потребители")
					this.$refs.realtimeChart.updateSeries(this.series, false, true);
				}

			}
		},
	    methods: {
    		filterChart(val){
    			if(val !== null && val !== '') {
    				this.series = []
    				this.chartOptions.labels = []
    				const beforeDateUsers = this.users.filter(o => new Date(o.created_at) < new Date(val))  	
    				const afterDateUsers = this.users.filter(o => new Date(o.created_at) >= new Date(val))  	
    				this.series.push(beforeDateUsers.length,afterDateUsers.length);
    				const labelDate = new Date(val).toLocaleDateString()
    				const newLabels = { labels: ['Регистрирани преди дата ' + labelDate , 'Регистрирани след дата ' + labelDate]}
    				this.$refs.realtimeChart.updateOptions(newLabels)
    				this.$refs.realtimeChart.updateSeries(this.series, false, true);
    			} else {
    				this.series = []
    				this.chartOptions.labels = []
    				let newLabelsData = []
    				if(this.unAuthorizedUsers !== undefined) {
    					this.series.push(this.unAuthorizedUsers.length)
    					newLabelsData.push("Неодобрени потребители")
    				}
    				if(this.users !== undefined) {
    					this.series.push(this.users.length)
    					newLabelsData.push("Одобрени потребители")
    				}
    				const newLabels = { labels: newLabelsData}
    				this.$refs.realtimeChart.updateOptions(newLabels)
    				this.$refs.realtimeChart.updateSeries(this.series, false, true);
    			}
	    	}
	    },
	}
</script>
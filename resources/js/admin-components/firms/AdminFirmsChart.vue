<template>
	<div>
		<div class="chart-wrap">
			<div class="filters_wrap">
				<date-picker v-model="filterDate" @change="filterChart" type="date" placeholder="Регистрирани след"></date-picker>
				<button class="btn small mt-10 w-100" @click="groupByFilter">Филтриране на потребители по фирми</button>
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
        computed: {
        	onlineUsers() {
        		return this.$store.state.onlineUsers
        	}
        },
        props: {
        	firms: {
        		type:Array
        	},
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
			firms(val) {
				this.series = []
				this.series.push(val.length)
				this.chartOptions.labels.push("Фирми")
				this.$refs.realtimeChart.updateSeries(this.series, false, true);
			}
		},
	    methods: {
    		filterChart(val){
    			if(val !== null && val !== '') {
    				this.series = []
    				this.chartOptions.labels = []
    				const beforeDateFirms = this.firms.filter(o => new Date(o.created_at) < new Date(val))  	
    				const afterDateFirms = this.firms.filter(o => new Date(o.created_at) >= new Date(val))  	
    				this.series.push(beforeDateFirms.length,afterDateFirms.length);
    				const labelDate = new Date(val).toLocaleDateString()
    				const newLabels = { labels: ['Фирми регистрирани преди дата ' + labelDate , 'Фирми регистрирани след дата ' + labelDate]}
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
    				if(this.firms !== undefined) {
    					this.series.push(this.firms.length)
    					newLabelsData.push("Фирми")
    				}
    				const newLabels = { labels: newLabelsData}
    				this.$refs.realtimeChart.updateOptions(newLabels)
    				this.$refs.realtimeChart.updateSeries(this.series, false, true);
    			}
	    	},
	    	groupByFilter() {
	    			const groupedMap = this.onlineUsers.reduce(
					    (entryMap, e) => entryMap.set(e.firmName, [...entryMap.get(e.firmName)||[], e]),
					    new Map()
					);

					this.series = []
    				this.chartOptions.labels = []

    				let labelsData = Array.from(groupedMap.keys())
    				const newLabels = { labels: labelsData}
    				for (const [key, value] of groupedMap.entries()) {
    					this.series.push(value.length)
					}

					this.$refs.realtimeChart.updateOptions(newLabels)
    				this.$refs.realtimeChart.updateSeries(this.series, false, true);
	    	},
	    	groupBy(xs,key) {
	    		return xs.reduce(function(rv, x) {
				    (rv[x[key]] = rv[x[key]] || []).push(x);
				    return rv;
				  }, {});
	    	}
	    }
	}
</script>
<template>
	<div>
		<div class="chart-wrap">
			<div class="filters_wrap">
				<date-picker v-model="filterDate" @change="filterChart" type="date" class="mb-12" placeholder="Създадени след"></date-picker>
	              <v-select :options="fromCities" class="mb-12" placeholder="Тръгване от" @input="filterByFrom"></v-select>
	              <v-select :options="toCities" class="mb-12" placeholder="Транспорт към" @input="filterByTo"></v-select>
			</div>
		    <div id="chart">
				<vue-apex-charts :width="700" type="pie" :options="chartOptions" :series="series" ref="realtimeChart"></vue-apex-charts>
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
        	transport: {
        		type:Array
        	},
        	toCities: {
        		type:Array
        	},
        	fromCities: {
        		type:Array
        	}
    	},
        data() {
        	return {
				series: [],
				loaded: false,
				filterDate: new Date(),
				filterFrom: '',
				filterTo: '',
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
						height: 250,
						width:200
					}
				},

        	}
		},
		mounted() {
			this.filterChart(null);
		},
		watch: {
			transport(val) {
				if(val !== undefined) {
					this.series = []
					this.series.push(val.length)
					this.chartOptions.labels.push("Товари")
					this.$refs.realtimeChart.updateSeries(this.series, false, true);
				}
			}
		},
	    methods: {
	    	filterByFrom(val){
	    		this.filterFrom = val
	    		this.filterFromEverything();
	    	},
	    	filterFromEverything() {

	    		this.clearData();
	    		//hold all info and filters
	    		let labelDate = '';
	    		let transportFrom = []
	    		let transportBefore = []
	    		let transportAfter = []
	    		let labelsFormated = []
	    		let transportNoDates = [] 

	    		if(this.filterDate !== null && this.filterDate !== '') { labelDate = this.filterDate.toLocaleDateString(); }

	    		//Only dates filter
	    		if((this.filterDate !== null && this.filterDate !== '') && (this.filterFrom == null || this.filterFrom == '') && (this.filterTo == '' || this.filterTo == null)) {
	    			transportBefore = this.transport.filter(o => new Date(o.created_at) >= new Date(this.filterDate)) 
	    			transportAfter = this.transport.filter(o => new Date(o.created_at) < new Date(this.filterDate))
	    			labelsFormated = ['Създаден преди дата ' + labelDate, 'Създаден след дата ' + labelDate]  
	    			this.series.push(transportBefore.length,transportAfter.length);
	    			const newLabels = { labels: labelsFormated}
					return this.updateChart(newLabels,this.series)
	    		}

	    		//With dates and TO filter and From filter
	    		if(this.filterDate !== null && this.filterDate !== '' && this.filterFrom != null && this.filterFrom != '' && this.filterTo != '' && this.filterTo != null) {
	    			transportBefore = this.transport.filter(o => new Date(o.created_at) >= new Date(this.filterDate)) 
	    			transportAfter = this.transport.filter(o => new Date(o.created_at) < new Date(this.filterDate))
    				transportBefore = transportBefore.filter(o => o.to_city === this.filterTo) 
					transportAfter = transportAfter.filter(o => o.to_city === this.filterTo)
					transportBefore = transportBefore.filter(o => o.from_city === this.filterFrom) 
					transportAfter = transportAfter.filter(o => o.from_city === this.filterFrom) 
	    			labelsFormated = ['Създаден преди дата ' + labelDate + ' и тръгване от ' + this.filterFrom + '  и разтоварване в ' + this.filterTo , 'Създаден след дата ' + labelDate + ' и тръгване от ' + this.filterFrom + ' и разтоварване в ' + this.filterTo]  
	    			this.series.push(transportBefore.length,transportAfter.length);
	    			const newLabels = { labels: labelsFormated}
					return this.updateChart(newLabels,this.series)
	    		}

	    		//With dates and FROM filter but with no To filter
	    		if(this.filterDate !== null && this.filterDate !== '' && this.filterFrom != null && this.filterFrom != '' && (this.filterTo == '' || this.filterTo == null)) {
	    			transportBefore = this.transport.filter(o => new Date(o.created_at) >= new Date(this.filterDate)) 
	    			transportAfter = this.transport.filter(o => new Date(o.created_at) < new Date(this.filterDate))
					transportBefore = transportBefore.filter(o => o.from_city === this.filterFrom) 
					transportAfter = transportAfter.filter(o => o.from_city === this.filterFrom) 
	    			labelsFormated = ['Създаден преди дата ' + labelDate + ' и тръгване от ' + this.filterFrom, 'Създаден след дата ' + labelDate + ' и тръгване от ' + this.filterFrom]  
	    			this.series.push(transportBefore.length,transportAfter.length);
	    			const newLabels = { labels: labelsFormated}
					return this.updateChart(newLabels,this.series)
	    		}

	    		//With dates and TO filter but with no FROM filter
	    		if(this.filterDate !== null && this.filterDate !== '' && this.filterFrom == null || this.filterFrom == '' && (this.filterTo != '' || this.filterTo != null)) {
	    			transportBefore = this.transport.filter(o => new Date(o.created_at) >= new Date(this.filterDate)) 
	    			transportAfter = this.transport.filter(o => new Date(o.created_at) < new Date(this.filterDate))
    				transportBefore = transportBefore.filter(o => o.to_city === this.filterTo) 
					transportAfter = transportAfter.filter(o => o.to_city === this.filterTo)
	    			labelsFormated = ['Създаден преди дата ' + labelDate + '  и разтоварване в ' + this.filterTo , 'Създаден след дата ' + labelDate + ' и разтоварване в ' + this.filterTo]  
	    			this.series.push(transportBefore.length,transportAfter.length);
	    			const newLabels = { labels: labelsFormated}
					return this.updateChart(newLabels,this.series)
	    		}
	    		
	    		//No dates but with From filter
	    		if(this.filterDate == null || this.filterDate == '' && this.filterFrom != '' && this.filterFrom != null && this.filterTo == null || this.filterTo == '') {
	    			transportFrom = this.transport.filter(o => o.from_city === this.filterFrom) 
	    			labelsFormated = ['Транспорт към ' + this.filterFrom ]
	    				this.series.push(transportFrom.length);
		    			const newLabels = { labels: labelsFormated}
						return this.updateChart(newLabels,this.series)
	    		}

	    		//No dates but with TO filter
	    		if(this.filterDate == null || this.filterDate == '' && this.filterFrom == '' || this.filterFrom == null && this.filterTo != null && this.filterTo != '') {
	    			transportFrom = this.transport.filter(o => o.to_city === this.filterTo) 
	    			labelsFormated = ['Транспорт към ' + this.filterFrom ]
	    				this.series.push(transportFrom.length);
		    			const newLabels = { labels: labelsFormated}
						return this.updateChart(newLabels,this.series)
	    		}
	    	},
	    	filterByTo(val) {
	    		this.filterTo = val
	    		this.filterFromEverything();
	    	},
	    	clearData() {
	    		this.series = []
	    		this.chartOptions.labels = []
	    	},
	    	updateChart(labels,series) {
	    		this.$refs.realtimeChart.updateOptions(labels)
				this.$refs.realtimeChart.updateSeries(series, false, true);
	    	},
	    	setDefaultData() {
				this.clearData()
	    		const newLabels = { labels: ['Транспорт']}
				this.series.push(this.transport.length);
				this.updateChart(newLabels,this.series)
	    	},
    		filterChart(val){
    			if(val !== null && val !== '') {
    				this.filterFromEverything();
    			} else {
    				this.setDefaultData()
    			}
	    	}
	    },
	}
</script>
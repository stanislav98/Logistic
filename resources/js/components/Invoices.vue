<template>
	<div class="invoices_wrap">
		<div class="card_box" 
			
			>
			<div class="card">
				<div class="table_header">
					<span>№</span>
					<span>Дата</span>
					<span>Статус</span>
					<span>Описание</span>
					<span>Цена</span>
					<span>Изтегли</span>
				</div>
				<div class="list_box" 
				v-for="item in this.invoices"
				v-bind:key="item.id">
   					<span>{{ item.id }}</span>
   					<span>{{ format_time(item.created) }}</span>
   					<span class="status" :class="setStatusClass(item.status)">{{ statusToCyr(item.status) }}</span>
   					<span>{{ item.lines.data[0].description }}</span>
   					<span class="price">{{ insertDecimal(item.amount_paid) }} лв.</span>
   					<a :href="item.hosted_invoice_url" class="download_button" target="_blank"><span class="material-icons">cloud_download</span></a>
				</div>


			</div>
		</div>
	</div>
</template>

<script>
	export default {
		computed: {
		    firm () {
		        return this.$store.state.firm
		    }, 
		    user () {
		        return this.$store.state.user
		    },
		    plans () {
		        return this.$store.state.plans
		    },
		    invoices () {
		    	return this.$store.state.invoices
		    }
		},
		data() {
			return {
				// invoices: []
	        }
		},
		created() {

		},
	    mounted() {
	    	if(this.invoices.length === 0) {
	    		console.log("will make call")
	            axios.get(`/api/invoices/${this.firm.id}`).then((res) => {
	            	const invoices = res.data.invoices;
	            	this.$store.commit('set_invoices',invoices)
				}).catch((error) => {
					console.log(error)
				})
			}
        },
        methods: {
        	insertDecimal(num) {
 			  return (num / 100).toFixed(2);
			},
			setStatusClass(status) {
				return status == 'paid' ? "paid" : "failed"
			},
			statusToCyr(status) {
				return status == 'paid' ? "Платена" : "Неплатена"
			},
			format_time(epochTime) {
				var date = new Date(0)
				date.setUTCSeconds(epochTime)
				// return d
			  var strArray=[
			  'Януари', 'Февруари', 'Март', 'Април', 'Май', 'Юни', 'Юли', 'Август', 'Септември', 'Октомври', 'Ноември', 'Декември'
			  ];
				    var d = date.getDate();
				    var m = strArray[date.getMonth()];
				    var y = date.getFullYear();
				    return '' + (d <= 9 ? '0' + d : d) + '/' + m + '/' + y;

			}
        }
	}
</script>
<template>
	<article class="single_tovar">
		<div class="heading">
			<div class="left_heading">
				<h3>От {{tovar.from_city}} до {{tovar.to_city}}</h3>
				<div class="tooltip"  v-if="tovar.fast_payment == 1">
					<span class="material-icons">payments</span>
					<span class="tooltiptext">Бързо плащане</span>
				</div>
				<div class="tooltip"   v-if="tovar.adr == 1">
					<img :src="'../images/adr.png'" alt="ADR">
					<span class="tooltiptext">ADR - Опасен товар</span>
				</div>
			</div>
			<p class="price">{{tovar.price}}лв</p>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">business</span>
				<span>{{tovar.firmName}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">today</span>
				<span>{{splitDate(tovar.from_date)}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">event_busy</span>
				<span>{{splitDate(tovar.to_date)}}</span>
			</p>
			<a href="#" class="btn message_now">
				<span class="material-icons">forward_to_inbox</span>Свържи се сега
			</a>
		</div>
		<div class="row">
			<p class="wrap_icon_text">
				<span class="material-icons">local_shipping</span>
				<span>{{tovar.vehicleName}}</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">add_road</span>
				<span>500.00 км.</span>
			</p>
			<p class="wrap_icon_text">
				<span class="material-icons">schedule</span>
				<span>1час 30мин</span>
			</p>
		</div>
		<p v-if="tovar.description" class="desc">{{tovar.description}}</p>
	</article>
</template>
<script>
export default{
	 	props: {
	 		tovar: {
	 			type: Object
	 		},
	 	},
		data(){
			return {
				errors:[],
			}
		},
		methods: {
			splitDate(date) {
				return date.split(' ')[0];
			}
		},
	}
</script>
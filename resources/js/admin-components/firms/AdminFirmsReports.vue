<template>
	<div class="card_box">
		<div class="card">
			<div class="pad_wrapper">
				<firm-report
					  v-for="firm in firms"
	                  v-bind:key="firm.id"
	                  v-bind:firm ="firm"
				></firm-report>
			</div>
		</div>
	</div>
</template>
<script>
	import FirmReport from './FirmReport.vue'

	export default {
		data() {
			return {

			}
		},
		props: {
			firms: {
				type: Array
			}
		},
		components: {
			FirmReport
		},
	}
</script>
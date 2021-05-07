<template>
	<div class="vehicles">
			<div class="top_err center"><span  class="error" v-if="errors">{{errors[0]}}</span></div>
				<div class="input_holder check_holder" :class="{'error' : errors[0] , 'disabled' : vehicle.disabled == 0 }" >
					<label class="container_checkbox">{{vehicle.name}}
						<input type="checkbox" @click="ad_checkbox" name="disabled" v-model="vehicle.disabled">
						<span class="checkmark"></span>
					</label>
					<div class="wrap_elements">
						<span class="material-icons">local_shipping</span>
						<input type="text" :disabled="vehicle.disabled == 0" :name="vehicle.name" :placeholder="vehicle.name" v-model="vehicle.count"  @keypress="onlyNumber">
					</div>
				</div>
	</div>
</template>
<script>
export default{
	 	props: {
	 		vehicle: {
	 			type: Object
	 		},

	 	},
		data(){
			return {
				errors:[],
			}
		},
		methods: {
			onlyNumber: function(evt) {
		      evt = (evt) ? evt : window.event;
		      var charCode = (evt.which) ? evt.which : evt.keyCode;
		      if(charCode >= 49 && charCode <=57) {
		      	return true;
		      } else {
		      	evt.preventDefault();
		      }
		    },
		    ad_checkbox(item) {
		    	if(item.name == 1) {
		    		item.name = 0;
		    	} else {
		    		item.name = 1;
		    		let subname = item.srcElement.name.substr(9);
		    		this.subname = '';
		    	}
		    }
		},
	}
</script>
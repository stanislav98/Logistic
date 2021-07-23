<template>
	<div class="star-rating">
        <label class="star-rating__star" v-for="rating in ratings" v-if="firm_id === undefined"
	        :class="{'is-selected': ((value >= rating) && value != 0), 'is-disabled': disabled}" 
	         v-on:mouseover="star_over(rating)" v-on:mouseout="star_out">
        	<input class="star-rating star-rating__checkbox" type="radio" :value="rating" :name="name"
    		v-model="value" :disabled="disabled" v-on:click="set(rating)">★
    	</label>
    	<span>{{ Number.parseFloat(this.allTimeValue).toPrecision(2) }} / {{ this.votesCount }} гласа</span>
    </div>
</template>
<script>
	export default {
		props: {
			firm_id: {
				type: Number
			}
		},
		computed: {
			user() {
				return this.$store.state.user
			}
		},
		data() {
			return {
				temp_value: 0,
      			ratings: [1, 2, 3, 4, 5],
      			value: 0,
      			allTimeValue: 0,
      			name: 'rate',
      			disabled: false,
      			votesCount: 0,
      			arrRates: []
			}
		},
		methods: {
			star_over: function(index) {
			  var self = this;
			  if (!this.disabled) {
			    this.temp_value = this.value;
			    return this.value = index;
			  }
			},

			star_out: function() {
			  var self = this;
			  if (!this.disabled) {
			    return this.value = this.temp_value;
			  }
			},

			set(value) {
				var self = this;

				axios.post(`/api/rating`,{firm_id: this.$route.params.id, user_id: this.user.id, rate: this.value })
				.then((response) => {
					if(response.data.type == 2) { // insert 
						this.arrRates.push(response.data.rating);
					} else if(response.data.type == 1){ // update
						let index = this.arrRates.findIndex(obj => obj.user_id === this.user.id)
						this.arrRates[index].rate = this.value
					}
					
					this.calcRates()
					this.$store.commit('set_notification',response.data.notification)
				})
				.catch(error => {
					this.$store.commit('set_notification',error.response.data.notification)
				})

				if (!this.disabled) {
					this.temp_value = value;
					return this.value = value;
				}
			},
			calcRates() {
				if(this.arrRates.length !== 0) {
					this.allTimeValue = 0
					const rate = this.arrRates.map((a,i) => this.allTimeValue += a.rate)
					this.allTimeValue = this.allTimeValue / this.arrRates.length
					this.votesCount = this.arrRates.length
					this.value = Math.floor(this.allTimeValue)
				} else {
					this.value = 0
					this.allTimeValue = 0
				}
			}
		},
		created() {
			let firmId = 0;
			if(this.$route.params.id !== undefined) {
				firmId = this.$route.params.id 
			} else {
				firmId = this.firm_id
			}

			axios.get(`/api/rating/${firmId}`)
			.then(resp => {
				this.arrRates = resp.data.rating
				this.calcRates()
			})
			.catch(err => {
				this.value = 0
				this.allTimeValue = 0
			})
		}
	}
</script>
<template>
	<div class="payment_page">
		<div class="card_box" v-if="this.user.has_payed == 0">
			<div class="card">
				<p>Заплатете вашият абонамент и започнете да използвате системата</p>
			</div>
		</div>
		<div class="card_box">
			<div class="card">
				<h2>Плащане на абонамент</h2>
				<div v-if="hasToPay() == false">
						<h4 class="mtb-10">Вашият абонамент е активен</h4>
						<p>Ако желаете да го откажете може да го направите от тук : </p>
						<div class="mt-10">
			            	<a href="#" @click="endPayment" class="btn_second">Отказ от абонамент</a>
			            </div>
				</div>
				<div class="wrap_subscriptions">
					<div class="card_box" 
						v-for="item in this.plans"
		       			v-bind:key="item.id"
		       			v-if="item.active === true"
						>
						<div class="card">
							<h4>{{ item.product.name }}</h4>
							<p>{{ insertDecimal(item.amount) }} лв.</p>
							<p>{{ item.product.description }}</p>
							<div v-if="hasToPay() == true">
								 <button
				                    class="btn"
				                    @click="processPayment"
				                    :disabled="paymentProcessing"
				                    v-text="paymentProcessing ? 'Плащането се обработва' : 'Плати сега'"
				                ></button>
							</div>
						</div>
					</div>
				</div>
				<div class="flex flex-wrap -mx-2 mt-4">
                    <label for="card-element">Информация за картата</label>
                    <div id="card-element" class="mtb-10"></div>
	            </div>
			</div>
		</div>
	</div>
</template>

<script>
    import { loadStripe } from '@stripe/stripe-js';
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
		},
		data() {
			return {
				stripe: {},
	            cardElement: {},
                paymentProcessing: false

	        }
		},
	    async mounted() {
            this.stripe = await loadStripe("pk_test_51IXWVQCYIxOqepVEwUC73PUupTURxvomV3dPQ6MFgpkVnEgT2GgsCPXowbXWS8P1maTWuuHySuX2cjT86o2udBcO00e8VJicdB");
            const elements = this.stripe.elements()
            this.cardElement = elements.create('card', {
                classes: {
                    base: 'bg-gray-100 rounded border border-gray-300 focus:border-indigo-500 text-base outline-none text-gray-700 p-3 leading-8 transition-colors duration-200 ease-in-out'
                }
            });

            this.cardElement.mount('#card-element')
            // if(!this.plans) {
	            axios.get('/api/subscribe',{firm: this.firm}).then((res) => {
	            	const plans = res.data.plans[0]
	            	this.$store.commit('set_plans',plans)
					}).catch((error) => {
				})
            // }
        },
        methods: {
        	insertDecimal(num) {
 			  return (num / 100).toFixed(2);
			},
			hasToPay() {
				return this.user.has_payed == 0 ? true : false
			},
			endPayment() {
				axios.post('/api/unsubscribe', {firm : this.firm}).then((response) => {
                    const notification = response.data.notification
					this.$store.commit('set_notification',notification)
	                })
	                .catch((error) => {
	                    this.paymentProcessing = false;
	                    const notification = response.data.notification
						this.$store.commit('set_notification',notification)
	                });
			},
        	async processPayment() {
                this.paymentProcessing = true;
                this.stripe
			    .createPaymentMethod({
			      type: 'card',
			      card: this.cardElement,
			      billing_details: {
			        name: this.firm.name,
			      },
			    })
			    .then((result) => {
			      if (result.error) {
			        this.paymentProcessing = false;
                    console.error(error);
			      } else {
			      	axios.post('/api/subscribe', {firm : this.firm, paymentMethod: result.paymentMethod, plan:this.plans[1]}).then((response) => {
                        const notification = response.data.notification
						this.$store.commit('set_notification',notification)
						const updated_user = this.user;
						updated_user.has_payed = true;
						this.$store.commit('update_user',updated_user);
	                })
	                .catch((error) => {
	                    this.paymentProcessing = false;
	                    const notification = response.data.notification
						this.$store.commit('set_notification',notification)
	                });
			      }
			    });
            }
        }
	}
</script>
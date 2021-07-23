<template>
	<div class="payment_page">
		<div class="card_box" v-if="hasPayed() === false">
			<div class="card">
				<p>Заплатете вашият абонамент и започнете да използвате системата</p>
			</div>
		</div>
		<div class="card_box">
			<div class="card">
				<div class="header_pricing">
					<h2>Плащане на абонамент</h2>
					<div class="cancel_payment_button" v-if="hasPayed() !== false">
		            	<a href="#" @click="endPayment" class="btn_second">Отказ от абонамент</a>
		            </div>
				</div>
				<div v-if="hasPayed() !== false">
						<h4 class="mtb-10">Вашият абонамент е активен</h4>
						<p>Може да се възползвате от всички услуги на системата</p>
						<p>Благодарим ви , че ни избрахте !</p>
				</div>
				<div class="flex flex-wrap -mx-2 mt-4" v-if="hasPayed() === false">
                    <label for="card-element">Информация за картата</label>
                    <div id="card-element" class="mtb-10"></div>
	            </div>
				<div class="wrap_subscriptions" v-if="hasPayed() === false">
					<div class="card_box" 
						v-for="item in this.plans"
		       			v-bind:key="item.id"
		       			v-if="item.active === true"
						>
						<div class="card">
							<div class="div_underlined pricing">
								<h4>{{ item.product.name }}</h4>
								<p class="price">{{ insertDecimal(item.amount) }} лв.</p>
							</div>
							<p>{{ item.product.description }}</p>
							<div class="btn_holder mt-10">
								 <button
				                    class="btn"
				                    @click="processPayment"
				                    :disabled="paymentProcessing"
				                    v-text="paymentProcessing === true ? 'Плащането се обработва' : 'Плати сега'"
				                ></button>
							</div>
						</div>
					</div>
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
		},
		data() {
			return {
				stripe: {},
	            cardElement: {},
                paymentProcessing: false,
                plans: [],

	        }
		},
	    async mounted() {
	    	if(this.hasPayed() !== true) {
	            this.stripe = await loadStripe("pk_test_51IXWVQCYIxOqepVEwUC73PUupTURxvomV3dPQ6MFgpkVnEgT2GgsCPXowbXWS8P1maTWuuHySuX2cjT86o2udBcO00e8VJicdB");
	            const elements = this.stripe.elements()
	            this.cardElement = elements.create('card', {
	                classes: {
	                    base: 'bg-gray-100 rounded border border-gray-300 focus:border-indigo-500 text-base outline-none text-gray-700 p-3 leading-8 transition-colors duration-200 ease-in-out'
	                }
	            });

	            this.cardElement.mount('#card-element')
		            axios.get('/api/subscribe',{firm: this.firm})
		            	.then((res) => {
			            	const plans = res.data.plans[0]
			            	this.plans = plans
						})
						.catch((error) => {
							const msg = "Нещо се обърка моля презаредете страницата!"
							const errorObj = {
								'msg': msg ,
			        			'type': 0, 
			        			'active': 1
							}
							this.$store.commit('set_notification',errorObj)
						})
			}
        },
        methods: {
        	insertDecimal(num) {
 			  return (num / 100).toFixed(2);
			},
			hasPayed() {
				return this.user.has_payed == 0 ? false : true
			},
			endPayment() {
				axios.post('/api/unsubscribe', {firm : this.firm})
				.then((response) => {
					console.log(response)
                    const notification = response.data.notification
					this.$store.commit('set_notification',notification)
                })
                .catch((error) => {
                    this.paymentProcessing = false;
                    const notification = error.data.response.notification
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
<template>
  <div class="reset_password">
    <h1 class="center">Нова парола</h1>
    <div class="card-body">
      <form autocomplete="off" @submit.prevent="resetPassword" method="post" class="center">
        <div class="input_holder">
          <div class="wrap_elements">
            <span class="material-icons">email</span>
            <input type="email" name="email" placeholder="Въведете вашият имейл" v-model="email">
          </div>
        </div>
        <div class="input_holder">
          <div class="wrap_elements">
            <span class="material-icons">https</span>
            <input type="password" name="password" placeholder="Парола" v-model="password">
          </div>
        </div>
        <div class="input_holder">
          <div class="wrap_elements">
            <span class="material-icons">https</span>
            <input type="password" name="password_confirmation" placeholder="Повторете паролата" v-model="password_confirmation">
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Обновяване</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
    data(){ 
      return { 
        token: null,
        email: null,
        password: null,
        password_confirmation: null,
      }
    },
    methods: {
        resetPassword() { 
          axios.post( '/api'+window.location.pathname,{token: this.$route.params.token,
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation
          })
          .then((res) => {
            this.$store.commit('set_notification',res.data.notification)
            this.$router.push({name: 'Login'})
          })
          .catch((error) => {
            this.$store.commit('set_notification',error.response.data.notification)
          })
        }
    }
}
        
    

</script>
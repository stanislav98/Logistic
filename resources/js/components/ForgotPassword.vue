<template>
  <div class="forgotten_password">
    <h1>Забравена парола</h1>
    <div class="free_content_message">
      <p>Ако сте забравили своята парола</p>
      <p>Може да използвате формата отдолу за да получите имейл</p>
      <p>от където ще може да обновите паролата си</p>
    </div>
    <div class="forgot_password">
      <form autocomplete="off" @submit.prevent="requestResetPassword" method="post">
        <div class="input_holder" :class="{'error' : errors.email }">
          <div class="wrap_elements">
            <span class="material-icons">email</span>
            <input type="email" name="email" placeholder="Въведете вашият имейл" v-model="email">
          </div>
          <span class="error" v-if="errors.email">{{errors.email[0]}}</span>
        </div>
        <div class="center">
          <button type="submit" class="btn">Изпрати нова парола</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
    data() {
      return {
        email: "staskata1998@gmail.com",
        errors: []
      }
    },
    methods: {
      requestResetPassword() {
        axios.post('/api/reset-password',{email: this.email}).then((res) => {
          this.response = res.data;
          const successObj = {
              'msg': "Успешно беше изпратен линк на имейла ви!" ,
              'type': 0, 
              'active': 1
            }
            this.$store.commit('set_notification',err.response.data.notification)
        }).catch((error) => {
            const errors = err.response.data.errors
            const msg = errors[Object.keys(errors)[0]][0]
            const errorObj = {
              'msg': msg ,
                  'type': 0, 
                  'active': 1
            }
            this.$store.commit('set_notification',err.response.data.notification)
        })
      }
    }
} 
</script>
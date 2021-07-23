<template>
  <div class="wrap_single_firm">  
    <div class="single-firm card_box">
      <div class="pad_wrapper">
        <div class="header_firm div_underlined">
          <h1>{{ this.firm.name }}</h1>
          <rating :firm_id="this.firm.id"></rating>
        </div class="header_firm">
        <p class="bulstat"><span class="material-icons">business</span><span>Булстат</span> : {{ this.firm.bulstat }}</p>
        <p class="manager"><span class="material-icons">person</span><span>Мениждър</span> : {{ this.firm.manager }}</p>
        <p class="address"><span class="material-icons">home</span><span>Адрес</span> : {{ this.firm.address }}</p>
        <div class="grid_two" v-if="loaded">
          <ul class="employees">
            <p class="ul_title">Служители</p>
            <li v-for="user in getUserDetails()">
              <p class="name">
                <span class="material-icons">person</span>
                <span>Име</span> : 
                <span>&nbsp;{{ user.name }}</span>
              </p>              
              <p class="email">
                <span class="material-icons">email</span>
                <span>Имейл</span> : 
                <a :href="`mailto:${user.email}`">&nbsp;{{ user.email }}</a>
              </p>              
              <p class="phone">
                <span class="material-icons">phone</span>
                <span>Телефонен номер</span> : 
                <a :href="`tel: ${user.phone}`">&nbsp;{{ user.phone }}</a>
              </p>              
            </li>
          </ul>
          <ul class="vehicles">
            <p class="ul_title">Автопарк</p>
            <li v-for="vehicle in getVehiclesDetails()">
              <p class="vehicle">
                <span class="material-icons ">local_shipping</span>
                <span>{{ vehicle.name }} : {{ vehicle.count }}</span>
              </p>              
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import Rating from '../../components/single-firm/Rating.vue';
  import Report from '../../components/single-firm/Report.vue';

  export default {
    data(){
      return {
        loaded: false
      }
    },
    components: {
      'rating': Rating,
      'reports': Report,
    },
    props: {
      firm: {
        type: Object
      }
    },
    methods: {
      getUserDetails(){
        let users = this.firm.users.split(',')
        let phone = this.firm.phones.split(',')
        let email = this.firm.emails.split(',')
        let user_ids = this.firm.user_ids.split(',')
        let result = {
          users: []
        }

        for (var i = 0; i < users.length; i++){
          let obj = {
            "name": users[i],
            "phone": phone[i],
            "email": email[i],
            "id": user_ids[i]
          }
          result.users.push(obj)        
        }

          return result.users;
      },
      getVehiclesDetails(){
        let count = this.firm.vehicle_counts.split(',')
        let vehicles = this.firm.vehicle_names.split(',')

        let result = {
          vehicles: []
        }

        for (var i = 0; i < vehicles.length; i++){
          let obj = {
            "name": vehicles[i],
            "count": count[i],
          }
          result.vehicles.push(obj)       
        }

          return result.vehicles;
      },
    },
    created() {
      if(this.firm !== undefined) {
        this.loaded = true
      }
    }

  }
</script>
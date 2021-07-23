<template>
  <div class="card_box admin_card_box">
    <div class="card">
      <h1 class="div_underlined">Потребители</h1>
      <ul class="list_actions">
         <li @click.prevent="activate(4)" :class="{ 'btn': active_el === 4, 'btn_second': active_el !== 4}">
          Статистика
        </li>
        <li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
          Активни потребители
        </li>
        <li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}">
          Неактивни потребители
        </li>
         <li @click.prevent="activate(3)" :class="{ 'btn': active_el === 3, 'btn_second': active_el !== 3}">
          Потребители чакащи одобрение
        </li>
      </ul>
      <div class="chart" v-if="this.active_el === 4">
        <user-chart :users="this.allUsers"></user-chart>
      </div>
      <div class="table" v-if="active_el !== 4">
        <div class="not_empty_table" v-if="sortedUsers.length !== 0">
          <div class="card sort_by in_admin">
            <input type="text" class="input" name="search_user_name" placeholder="Търсене по име" @input="clearPage" v-model="filterUserName">
            <button @click.prevent="clearSearch" class="btn">Изчисти търсенето</button>
          </div>
          <ul class="table_holder">
            <admin-single-user
              v-for="user in sortedUsers"
                  v-bind:key="user.id"
                  v-bind:user ="user"
                  @clicked="activate"
            ></admin-single-user>
          </ul>
          <paginate
            v-model="page"
            :page-count="pagesCount"
            :page-range="3"
            :margin-pages="2"
            :prev-text="'&#8592;'"
            :next-text="'&#8594;'"
            :hide-prev-next="true"
            :container-class="'pagination'"
            :page-class="'page-item'">
          </paginate>
        </div>
        <div v-else>
            <div class="card_box pad_wrapper">
              <h3>Няма намерени потребители</h3>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import AdminSingleUser from './AdminSingleUser.vue';
  import UserChart from './UserChart.vue';

  export default {
    data() {
      return {
        users: [], // filters based on all users
        allUsers: [], //holds all users fetched from api
        unAuthorizedUsers: [],
        perPage: 6,
        page: 1,
        pages: [],
        pagesCount: 0,
        filterUserName: '',
        active_el: 4,
      }
    },
    components: {
        'admin-single-user': AdminSingleUser,
        'user-chart': UserChart
    },
    computed: {
      user() {
        return this.$store.state.user
      },
      sortedUsers() {

            let sorted_users = this.users;

            if(this.filterUserName) {
              sorted_users = sorted_users.filter(i => (String(i.name).toLowerCase().includes(this.filterUserName.toLowerCase())) ? i : '')
            }

            this.setPages(sorted_users);
            sorted_users = this.paginate(sorted_users);

            return sorted_users
      }
    },
    methods: {
      activate(el){
        this.page=1
        if(el === 2) {
          this.users = this.allUsers.filter((o) => o.has_payed === 0)
        } else if(el === 1){
          this.users = this.allUsers.filter((o) => o.id !== this.user.id)
        } else if(el === 3) {
          this.users = this.unAuthorizedUsers
        } 
        this.active_el = el
      },
      paginate (users) {
          let page = this.page;
          let perPage = this.perPage;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return users.slice(from, to);
        },
        nextPage() {
          if((this.currentPage*this.pageSize) < this.users.length) this.currentPage++;
        },
        prevPage() {
          if(this.currentPage > 1) this.currentPage--;
        },
        setPages(users) {
          let numberOfPages = Math.ceil(users.length / this.perPage);
          this.pagesCount = numberOfPages
          for (let index = 1; index <= numberOfPages; index++) {
            this.pages.push(index);
          }
          this.paginate(users)
        },
        clearPage() {
          this.page=1
        },
        clearSearch() {
          this.page = 1
          this.filterUserName = '';
        },
    },
    created() {
      axios.get('/api/users')
      .then(resp => {
        this.allUsers = resp.data.users
        this.users = this.allUsers.filter((o) => o.id !== this.user.id)

          axios.get('/api/users/unauthorized')
          .then(resp => {
            this.unAuthorizedUsers = resp.data.unauthorizedUsers
          })
          .catch((error) => {
            this.$store.commit('set_notification',err.response.data.notification)
          })
      })
      .catch(err => {
        this.$store.commit('set_notification',err.response.data.notification)
      })
    }
  }
</script>
<template>
  <div class="card_box admin_card_box">
    <div class="card">
      <h1 class="div_underlined">Транспорт</h1>
      <ul class="list_actions">
         <li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}">
          Статистика
        </li>
        <li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
          Транспорт
        </li>
      </ul>
      <div class="chart" v-if="this.active_el === 2">
        <transport-chart :transport="this.transports" :fromCities="this.fromCities" :toCities="this.toCities"></transport-chart>
      </div>
      <div class="table" v-if="active_el !== 2">
        <div class="not_empty_table">
         <div class="card_box sort_by in_admin">
            <div class="card">
              <div class="row sort_pad_admin">
                <a class="wrap_icon_text btn"  @click="sort('price')"
                :class="[this.currentSortDir === 'asc' ? 'danger' : '', this.currentSortDir  === 'desc' ? 'green' : '']">
                  <span class="material-icons" v-if="this.currentSortDir === 'asc'">arrow_upward</span>
                  <span class="material-icons" v-if="this.currentSortDir === 'desc'">arrow_downward</span>
                  <span>Цена</span>
                </a>
                <date-picker v-model="sortByDateFrom" type="datetime" placeholder="Изберете дата на транспорт"></date-picker>
                <date-picker v-model="sortByDateTo" type="datetime" placeholder="Изберете дата на разтоварване"></date-picker>
                <p class="wrap_icon_text">
                  <span class="material-icons">place</span>
                  <v-select :options="fromCities" placeholder="Тръгване от" @input="setFilterFrom"></v-select>
                </p>
                <p class="wrap_icon_text">
                  <span class="material-icons">pin_drop</span>
                  <v-select :options="toCities" placeholder="Транспорт към" @input="setFilterTo"></v-select>
                </p>
              </div>
            </div>
          </div>
          <ul class="table_holder one">
            <admin-single-transport
              v-for="transport in sortedTransport"
                  v-bind:key="transport.id"
                  v-bind:transport ="transport"
                  @clicked="activate"
            ></admin-single-transport>
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
      </div>
    </div>
  </div>
</template>
<script>
  import AdminSingleTransport from './AdminSingleTransport.vue';
  import TransportChart from './TransportChart.vue';
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css';
  import 'vue2-datepicker/locale/bg';

  export default {
    data() {
      return {
        transports: [], // filters based on all transports
        currentSortDir: 'desc',
        perPage: 6,
        page: 1,
        pages: [],
        pagesCount: 0,
        filterName: '',
        filterBulstat: '',
        active_el: 2,
        sortByDates: [],
        sortByDateFrom: '',
        sortByDateTo: '',
        filterFrom: '',
        filterTo: '',
      }
    },
    components: {
        'admin-single-transport': AdminSingleTransport,
        'transport-chart': TransportChart,
        'date-picker': DatePicker 
    },
    computed: {
      user() {
        return this.$store.state.user
      },
      sortedTransport() {

            let sorted_transport = this.transports;


            if(this.currentSortDir ==='asc') {
              sorted_transport = this.transports.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            } else {
              sorted_transport = this.transports.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            }

            if(this.filterFrom) {
              sorted_transport = sorted_transport.filter(i => (i.from_city == this.filterFrom) ? i : '')
            }
            if(this.filterTo) {
              sorted_transport = sorted_transport.filter(i => (i.to_city == this.filterTo) ? i : '')
            }

            if(this.sortByDateFrom !== '' && this.sortByDateFrom !== null) {
              sorted_transport = sorted_transport.filter(i => (new Date(i.from_date) > new Date(this.sortByDateFrom)) ? i : '')
            }

            if(this.sortByDateTo !== '' && this.sortByDateTo !== null) {
              sorted_transport = sorted_transport.filter(i => (i.to_date == this.sortByDateTo) ? i : '')
            }

            this.setPages(sorted_transport);
            sorted_transport = this.paginate(sorted_transport);

            return sorted_transport
      },
      fromCities() {
        if(this.transports !== undefined) {
          return this.transports.map(a => a.from_city).filter((value, index, self) => self.indexOf(value) === index);
        }
      },
      toCities() {
        if(this.transports !== undefined) {
          return this.transports.map(a => a.to_city).filter((value, index, self) => self.indexOf(value) === index);
        }
      },
    },
    methods: {
      activate(el){
        this.page = 1
        this.active_el = el
      },
      sort(s) {
          this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
      },
      setFilterFrom(value) {
        this.filterFrom = value
        this.page = 1
      },
      setFilterTo(value) {
        this.filterTo = value
        this.page = 1
      },
      paginate (users) {
          let page = this.page;
          let perPage = this.perPage;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return users.slice(from, to);
        },
        nextPage() {
          if((this.currentPage*this.pageSize) < this.transports.length) this.currentPage++;
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
      axios.get('/api/transport')
      .then(resp => {
        this.transports = resp.data.transport
      })
      .catch(err => {
        this.$store.commit('set_notification',err.response.data.notification)
      })
    }
  }
</script>
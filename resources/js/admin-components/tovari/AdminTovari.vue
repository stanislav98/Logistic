<template>
  <div class="card_box admin_card_box">
    <div class="card">
      <h1 class="div_underlined">Товар</h1>
      <ul class="list_actions">
         <li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}">
          Статистика
        </li>
        <li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
          Товар
        </li>
      </ul>
      <div class="chart" v-if="this.active_el === 2">
        <tovari-chart :tovar="this.tovari" :fromCities="this.fromCities" :toCities="this.toCities"></tovari-chart>
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
                <date-picker v-model="sortByDateFrom" type="datetime" placeholder="Изберете дата на товарене"></date-picker>
                <date-picker v-model="sortByDateTo" type="datetime" placeholder="Изберете дата на разтоварване"></date-picker>
                <p class="wrap_icon_text">
                  <span class="material-icons">place</span>
                  <v-select :options="fromCities" placeholder="Тръгване от" @input="setFilterFrom"></v-select>
                </p>
                <p class="wrap_icon_text">
                  <span class="material-icons">pin_drop</span>
                  <v-select :options="toCities" placeholder="Товар към" @input="setFilterTo"></v-select>
                </p>
              </div>
            </div>
          </div>
          <ul class="table_holder one">
            <admin-single-tovar
              v-for="tovar in sortedTovari"
                  v-bind:key="tovar.id"
                  v-bind:tovar ="tovar"
                  @clicked="activate"
            ></admin-single-tovar>
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
  import AdminSingleTovar from './AdminSingleTovar.vue';
  import TovariChart from './TovariChart.vue';
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css';
  import 'vue2-datepicker/locale/bg';

  export default {
    data() {
      return {
        tovari: [], // filters based on all tovari
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
        'admin-single-tovar': AdminSingleTovar,
        'tovari-chart': TovariChart,
        'date-picker': DatePicker 
    },
    computed: {
      user() {
        return this.$store.state.user
      },
      sortedTovari() {

            let sorted_tovari = this.tovari;


            if(this.currentSortDir ==='asc') {
              sorted_tovari = this.tovari.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            } else {
              sorted_tovari = this.tovari.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            }

            if(this.filterFrom) {
              sorted_tovari = sorted_tovari.filter(i => (i.from_city == this.filterFrom) ? i : '')
            }
            if(this.filterTo) {
              sorted_tovari = sorted_tovari.filter(i => (i.to_city == this.filterTo) ? i : '')
            }

            if(this.sortByDateFrom !== '' && this.sortByDateFrom !== null) {
              sorted_tovari = sorted_tovari.filter(i => (new Date(i.from_date) > new Date(this.sortByDateFrom)) ? i : '')
            }

            if(this.sortByDateTo !== '' && this.sortByDateTo !== null) {
              sorted_tovari = sorted_tovari.filter(i => (i.to_date == this.sortByDateTo) ? i : '')
            }

            this.setPages(sorted_tovari);
            sorted_tovari = this.paginate(sorted_tovari);

            return sorted_tovari
      },
      fromCities() {
        if(this.tovari !== undefined) {
          return this.tovari.map(a => a.from_city).filter((value, index, self) => self.indexOf(value) === index);
        }
      },
      toCities() {
        if(this.tovari !== undefined) {
          return this.tovari.map(a => a.to_city).filter((value, index, self) => self.indexOf(value) === index);
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
          if((this.currentPage*this.pageSize) < this.tovari.length) this.currentPage++;
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
      axios.get('/api/tovari')
      .then(resp => {
        this.tovari = resp.data.tovari
      })
      .catch(err => {
        this.$store.commit('set_notification',err.response.data.notification)
      })
    }
  }
</script>
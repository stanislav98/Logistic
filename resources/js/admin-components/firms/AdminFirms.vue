<template>
  <div class="card_box admin_card_box">
    <div class="card">
      <h1 class="div_underlined">Фирми</h1>
      <ul class="list_actions">
         <li @click.prevent="activate(4)" :class="{ 'btn': active_el === 4, 'btn_second': active_el !== 4}">
          Статистика
        </li>
        <li @click.prevent="activate(1)" :class="{ 'btn': active_el === 1, 'btn_second': active_el !== 1}">
          Фирми
        </li>
        <li @click.prevent="activate(2)" :class="{ 'btn': active_el === 2, 'btn_second': active_el !== 2}">
          Докладвания
        </li>
      </ul>
      <div class="chart" v-if="this.active_el === 4">
        <firms-chart :firms="this.firms"></firms-chart>
      </div>
      <div class="table" v-if="active_el == 1">
        <div class="card_box filter">
          <div class="card sort_by">
            <input type="text" name="search_name" placeholder="Търсене по име на фирма" @input="clearPage" v-model="filterName">
            <input type="text" name="search_bulstat" placeholder="Търсене по булстат" @input="clearPage" v-model="filterBulstat">
            <button @click.prevent="clearSearch" class="btn">Изчисти търсенето</button>
          </div>
        </div>
        <div class="not_empty_table" v-if="sortedFirms.length !== 0">
          <ul class="table_holder one">
            <admin-firm-single
              v-for="firm in sortedFirms"
                  v-bind:key="firm.id"
                  v-bind:firm ="firm"
                  @clicked="activate"
            ></admin-firm-single>
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
          <h3>Няма намерени резултати</h3>
        </div>
      </div>
      <div v-if="active_el == 2">
        <admin-firm-reports :firms="firms"></admin-firm-reports>
      </div>
    </div>
  </div>
</template>
<script>
  import AdminFirmSingle from './AdminFirmSingle.vue';
  import AdminFirmsChart from './AdminFirmsChart.vue';
  import AdminFirmsReports from './AdminFirmsReports.vue';

  export default {
    data() {
      return {
        firms: [], // filters based on all firms
        perPage: 6,
        page: 1,
        pages: [],
        pagesCount: 0,
        active_el: 4,
        filterName: '',
        filterBulstat: '',
      }
    },
    components: {
        'admin-firm-single': AdminFirmSingle,
        'firms-chart': AdminFirmsChart,
        'admin-firm-reports': AdminFirmsReports
    },
    computed: {
      user() {
        return this.$store.state.user
      },
      sortedFirms() {

            let sorted_firms = this.firms;

            if(this.filterBulstat) {
              sorted_firms = sorted_firms.filter(i => (String(i.bulstat).toLowerCase().includes(this.filterBulstat)) ? i : '')
            }

            if(this.filterName) {
              sorted_firms = sorted_firms.filter(i => (String(i.name).toLowerCase().includes(this.filterName)) ? i : '')
            }

            this.setPages(sorted_firms);
            sorted_firms = this.paginate(sorted_firms);

            return sorted_firms
      }
    },
    methods: {
      activate(el){
        this.page=1
        this.active_el = el
      },
      paginate (firms) {
          let page = this.page;
          let perPage = this.perPage;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return firms.slice(from, to);
        },
        nextPage() {
          if((this.currentPage*this.pageSize) < this.firms.length) this.currentPage++;
        },
        prevPage() {
          if(this.currentPage > 1) this.currentPage--;
        },
        setPages(firms) {
          let numberOfPages = Math.ceil(firms.length / this.perPage);
          this.pagesCount = numberOfPages
          for (let index = 1; index <= numberOfPages; index++) {
            this.pages.push(index);
          }
          this.paginate(firms)
        },
        clearPage() {
          this.page=1
        },
        clearSearch() {
          this.page = 1
          this.filterName = '';
          this.filterBulstat =  ''
        },
    },
    created() {
      axios.get('/api/firms')
      .then(resp => {
        this.firms = resp.data.firms
      })
      .catch(err => {
        this.$store.commit('set_notification',err.response.data.notification)
      })
    }
  }
</script>
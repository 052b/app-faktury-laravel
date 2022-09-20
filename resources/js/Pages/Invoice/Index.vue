<template>
  <Head title="Faktury"/>

  <authenticated-layout>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Faktury</h3>
        <div class="card-actions">
          <Link class="btn btn-primary" :href="route('invoices.create')">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nowy rekord
          </Link>
        </div>
      </div>
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="col-2">
            <label class="form-label">Kontrahent</label>
            <VueMultiselect v-model="selectedClient"
                            track-by="id"
                            label="name"
                            placeholder="Wybierz"
                            :options="clients"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
            >
            </VueMultiselect>
          </div>
          <div class="col-2">
            <label class="form-label">Od</label>
            <date-picker v-model:value="dateFrom"
                         type="date"
                         value-type="date"
                         clearable="false"
            ></date-picker>
          </div>
          <div class="col-2">
            <label class="form-label">Do</label>
            <date-picker v-model:value="dateTo"
                         type="date"
                         value-type="date"
                         clearable="false"
            ></date-picker>
          </div>
          <div class="col-2 mt-3">
            <button class="btn btn-outline" @click.prevent="onFilter">Filtruj</button>
            <button class="btn btn-outline" @click.prevent="onClearFilters">Resetuj filtrację</button>
          </div>
          <div class="ms-auto text-muted">
            <label class="form-label">Szukaj:</label>
            <input type="text"
                   v-model="query.search"
                   class="form-control"
                   aria-label="Szukaj"
            >
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
          <thead>
          <tr>
            <th>x</th>
            <th><span @click="sort('invoice_number')">Numer</span>
              <component v-bind:is="currentSort('invoice_number')"></component>
            </th>
            <th><span @click="sort('client_name')">Kontrahent</span>
              <component v-bind:is="currentSort('client_name')"></component>
            </th>
            <th><span @click="sort('create_date')">Data wystawienia</span>
              <component v-bind:is="currentSort('create_date')"></component>
            </th>
            <th><span @click="sort('sell_date')">Data sprzedaży</span>
              <component v-bind:is="currentSort('sell_date')"></component>
            </th>
            <th>Termin płatności</th>
            <th><span @click="sort('status')">Status</span>
              <component v-bind:is="currentSort('status')"></component>
            </th>
            <th>Kwota netto</th>
            <th>Akcje</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="invoice in invoices.data" :key="invoice.id">
            <td>X</td>
            <td>
              <Link class="text-reset text-end" :href="`/invoices/${invoice.id}/edit`">
                {{ invoice.invoice_number }}
              </Link>
            </td>
            <td>
              <Link class="text-reset" :href="`/invoices/${invoice.id}/edit`">
                {{ invoice.client_name }}
              </Link>
            </td>
            <td>
              <Link class="text-reset" :href="`/invoices/${invoice.id}/edit`">{{ invoice.create_date }}</Link>
            </td>
            <td>{{ invoice.sell_date }}</td>
            <td class="text-end">{{ invoice.invoice_term }}</td>
            <td>
              <set-paid-status :invoice-id="invoice.id" :invoice-status="invoice.status"></set-paid-status>
            </td>
            <td class="text-end">{{ invoice.positions_sum_price_netto }}</td>
            <td>
              <div class="btn-group btn-group-sm" role="group" aria-label="CRUD">
                <button>Edycja</button>
                <button>Pdf</button>
                <button>Duplikuj</button>
                <button @click="destroy(invoice.id)"
                        type="button"
                        class="btn btn-warning"
                >
                  Usuń
                </button>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
        <table-empty v-if="invoices.data.length === 0"></table-empty>
      </div>
      <div class="card-footer d-flex align-items-center">
        <page-of-pages :from="invoices.from" :to="invoices.to" :total="invoices.total"></page-of-pages>
        <paginate :model-value="invoices.current_page"
                  :path="invoices.path"
                  :page-count="invoices.last_page"
                  :margin-pages="2"
                  :page-range="5"
                  container-class="pagination m-0 ms-auto"
                  prev-text="Poprzednia"
                  next-text="Następna"
        ></paginate>
      </div>
    </div>
    <date-paid-modal title="Data płatności" btn-cancel="Anuluj" btn-ok="Potwierdź"></date-paid-modal>
  </authenticated-layout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head, Link} from '@inertiajs/inertia-vue3'
import {Inertia} from '@inertiajs/inertia'
import Paginate from "@/Components/Paginate";
import PageOfPages from "@/Components/PageOfPages";
import TableEmpty from "@/Components/TableEmpty";
import {throttle, findIndex, has} from 'lodash';
import VueMultiselect from 'vue-multiselect'
import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import 'vue-datepicker-next/locale/pl';
import {SortDescending2Icon, SortAscending2Icon} from 'vue-tabler-icons'
import SetPaidStatus from "@/Components/Invoice/SetPaidStatus";
import DatePaidModal from "@/Components/Invoice/DatePaidModal";

export default {
  components: {
    PageOfPages,
    Paginate,
    TableEmpty,
    AuthenticatedLayout,
    Head,
    Link,
    VueMultiselect,
    DatePicker,
    SortDescending2Icon,
    SortAscending2Icon,
    SetPaidStatus,
    DatePaidModal
  },
  props: {
    invoices: Object,
    clients: Object,
    filters: Object
  },
  data() {
    return {
      sortedColumn: 'create_date',
      dateFrom: null,
      dateTo: null,
      dateFromCopy: null,
      dateToCopy: null,
      query: {
        field: this.filters.sort ? this.filters.sort.split('-').pop() : null,
        direction: this.filters.sort ? (this.filters.sort.indexOf('-') > -1 ? 'desc' : 'asc') : null
      },
      selectedClient: {}
    }
  },
  mounted() {
    const today = new Date()
    this.dateFrom = new Date(today.getFullYear(), 0, 1)
    this.dateTo = new Date(today.getFullYear(), 11, 31)
    this.dateFromCopy = new Date(today.getFullYear(), 0, 1)
    this.dateToCopy = new Date(today.getFullYear(), 11, 31)

    let clientIdx = null
    if (has(this.filters.filter, 'client')) {
      clientIdx = findIndex(this.clients, {'id': parseInt(this.filters.filter.client)})
    }

    if (clientIdx) {
      this.selectedClient = this.clients[clientIdx]
    }
  },
  setup() {
    const destroy = (id) => {
      if (confirm("Czy chcesz usunąć ten rekord?")) {
        Inertia.delete(route('invoices.destroy', id))
      }
    }
    return {destroy}
  },
  watch: {
    query: {
      handler: throttle(function () {
        this.$inertia.get(route('invoices.index'), this.getFilterParams(), {
          preserveState: true,
          replace: true
        });
      }, 150),
      deep: true
    }
  },
  computed: {
    currentSort() {
      return (column) => {
        if (column === this.sortedColumn) {
          if (this.query.direction === 'desc') {
            return SortAscending2Icon
          }
          return SortDescending2Icon
        }

        return null
      }
    }
  },
  methods: {
    onFilter() {
      this.getData(this.getFilterParams())
    },
    onClearFilters() {
      this.selectedClient = {}
      this.dateFrom = this.dateFromCopy
      this.dateTo = this.dateToCopy

      this.getData({})
    },
    sort(field) {
      this.sortedColumn = field
      this.query.direction = this.query.direction === 'asc' ? 'desc' : 'asc';
      this.query.field = (this.query.direction === 'asc' ? '-' : '') + field;
    },
    getFilterParams() {
      const dateFrom = this.dateFrom.getFullYear() + '-' + (this.dateFrom.getMonth() + 1) + '-' + this.dateFrom.getDate()
      const dateTo = this.dateTo.getFullYear() + '-' + (this.dateTo.getMonth() + 1) + '-' + this.dateTo.getDate()

      return {
        'filter[created]': dateFrom + ',' + dateTo,
        ...(this.selectedClient.id && {'filter[client]': this.selectedClient.id}),
        ...(this.query.search && {'filter[invoice]': this.query.search}),
        'sort': this.query.field
      }
    },
    getData(params) {
      this.$inertia.get(route('invoices.index'), params, {
        preserveState: true,
        replace: true
      });
    }
  }
}
</script>

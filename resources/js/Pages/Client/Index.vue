<template>
  <Head title="Kontrahenci"/>

  <authenticated-layout>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Kontrahenci</h3>
        <div class="card-actions">
          <Link class="btn btn-primary" :href="route('clients.create')">
            <plus-icon></plus-icon>
            Nowy rekord
          </Link>
        </div>
      </div>
      <div class="card-body border-bottom py-3">
        <div class="d-flex">
          <div class="text-muted col-2">
            Pokaż
            <div class="mx-2 d-inline-block">
              <VueMultiselect v-model="selectedStatus"
                              track-by="id"
                              label="name"
                              placeholder="Wybierz"
                              :options="options"
                              :searchable="false"
                              :allow-empty="false"
                              :show-labels="false"
                              @update:model-value="onFilterStatus"
              >
              </VueMultiselect>
            </div>
          </div>
          <div class="ms-auto text-muted">
            Szukaj:
            <div class="ms-2 d-inline-block">
              <input type="text"
                     v-model="query.search"
                     class="form-control"
                     aria-label="Szukaj"
              >
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table card-table table-vcenter datatable">
          <thead>
          <tr>
            <th>&nbsp;</th>
            <th>
              <span @click="sort('name')">Nazwa</span>
              <component v-bind:is="currentSort('name')"></component>
            </th>
            <th>
              <span @click="sort('address')">Adres</span>
              <component v-bind:is="currentSort('address')"></component>
            </th>
            <th>
              <span @click="sort('city')">Miasto</span>
              <component v-bind:is="currentSort('city')"></component>
            </th>
            <th>
              <span @click="sort('nip')">NIP</span>
              <component v-bind:is="currentSort('nip')"></component>
            </th>
            <th><span @click="sort('phone')">Telefon</span></th>
            <th><span @click="sort('mail')">E-mail</span></th>
            <th>Archiwum</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="client in clients.data" :key="client.id">
            <td>
              <key-icon></key-icon>
            </td>
            <td>
              <Link class="text-reset" :href="`/clients/${client.id}/edit`">{{ client.name }}</Link>
            </td>
            <td>
              <Link class="text-reset" :href="`/clients/${client.id}/edit`">
                {{ getAddress(client.address, client.address2) }}
              </Link>
            </td>
            <td>
              <Link class="text-reset" :href="`/clients/${client.id}/edit`">{{
                  zipCity(client.zip, client.city)
                }}
              </Link>
            </td>
            <td>{{ client.nip }}</td>
            <td>{{ client.phone }}</td>
            <td>{{ client.mail }}</td>
            <td>
              <input class="form-check-input"
                     type="checkbox"
                     v-model="client.deleted"
                     @click="toggle(client.id)"
              >
            </td>
          </tr>
          </tbody>
        </table>
        <TableEmpty v-if="clients.data.length === 0"></TableEmpty>
      </div>
      <div class="card-footer d-flex align-items-center">
        <page-of-pages :from="clients.from" :to="clients.to" :total="clients.total"></page-of-pages>
        <paginate :model-value="clients.current_page"
                  :path="clients.path"
                  :page-count="clients.last_page"
                  :margin-pages="2"
                  :page-range="5"
                  container-class="pagination m-0 ms-auto"
                  prev-text="Poprzednia"
                  next-text="Następna"
        ></paginate>
      </div>
    </div>
  </authenticated-layout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head, Link} from '@inertiajs/inertia-vue3'
import Paginate from "@/Components/Paginate";
import PageOfPages from "@/Components/PageOfPages";
import TableEmpty from "@/Components/TableEmpty"
import {throttle, findIndex} from 'lodash';
import VueMultiselect from 'vue-multiselect'
import {SortDescending2Icon, SortAscending2Icon, KeyIcon, PlusIcon} from 'vue-tabler-icons'

export default {
  components: {
    PageOfPages,
    Paginate,
    TableEmpty,
    AuthenticatedLayout,
    Head,
    Link,
    VueMultiselect,
    KeyIcon,
    SortDescending2Icon,
    SortAscending2Icon,
    PlusIcon
  },
  props: {
    clients: Object,
    filters: Object
  },
  data() {
    return {
      options: [
        {id: 0, name: 'Aktywne'},
        {id: 1, name: 'Archiwalne'},
        {id: 2, name: 'Wszystkie'}
      ],
      query: {
        search: this.filters.filter?.client,
        status: this.filters.filter?.deleted,
        field: this.filters.sort ? this.filters.sort.split('-').pop() : null,
        direction: this.filters.sort ? (this.filters.sort.indexOf('-') > -1 ? 'desc' : 'asc') : null
      },
      selectedStatus: {},
      sortedColumn: 'name',
    }
  },
  mounted() {
    let statusIdx = 0

    if (this.query.status !== undefined) {
      statusIdx = findIndex(this.options, {'id': parseInt(this.query.status)})
    }

    if (statusIdx === -1) {
      statusIdx = 2
    }
    this.selectedStatus = this.options[statusIdx]
  },
  watch: {
    query: {
      handler: throttle(function () {
        const params = {
          'filter[client]': this.query.search,
          'filter[deleted]': this.query.status,
          'sort': this.query.field
        }

        this.$inertia.get(route('clients.index'), params, {
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
    onFilterStatus(value) {
      switch (value.id) {
        case 0 :
          this.query.status = 0
          break
        case 1 :
          this.query.status = 1
          break
        case 2 :
          this.query.status = null
          break
      }
    },
    sort(field) {
      this.query.direction = this.query.direction === 'asc' ? 'desc' : 'asc';
      this.query.field = (this.query.direction === 'asc' ? '-' : '') + field;
    },
    zipCity(zip, city) {
      return zip + ' ' + (city ?? '').trim()
    },
    getAddress(address, address2) {
      return (address + ' ' + (address2 ?? '')).trim()
    },
    toggle(id) {
      axios.delete(route('clients.destroy', id))
          .then(response => {
            console.log(response)
          })
          .catch(error => {
            console.log(error)
          })
    }
  }
}
</script>

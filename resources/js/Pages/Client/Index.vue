<template>
  <Head title="Kontrahenci"/>

  <authenticated-layout>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Kontrahenci</h3>
        <div class="card-actions">
          <Link class="btn btn-primary" :href="route('clients.create')">
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
                     class="form-control form-control-sm"
                     aria-label="Szukaj"
              >
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
          <thead>
          <tr>
            <th>&nbsp;</th>
            <th><span @click="sort('name')">Nazwa</span></th>
            <th><span @click="sort('address')">Adres</span></th>
            <th><span @click="sort('city')">Miasto</span></th>
            <th><span @click="sort('nip')">NIP</span></th>
            <th><span @click="sort('phone')">Telefon</span></th>
            <th><span @click="sort('mail')">E-mail</span></th>
            <th>Archiwum</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="client in clients.data" :key="client.id">
            <td><KeyIcon></KeyIcon></td>
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
            <td>{{ client.deleted }}</td>
            <td>
              <div class="btn-group btn-group-sm" role="group" aria-label="CRUD">
                <button @click="destroy(client.id)"
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
import {Inertia} from '@inertiajs/inertia'
import Paginate from "@/Components/Paginate";
import PageOfPages from "@/Components/PageOfPages";
import {throttle, findIndex} from 'lodash';
import VueMultiselect from 'vue-multiselect'
import KeyIcon from 'vue-tabler-icons'

export default {
  components: {
    PageOfPages,
    Paginate,
    AuthenticatedLayout,
    Head,
    Link,
    VueMultiselect,
    KeyIcon
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
      selectedStatus: {}
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
  setup() {
    const destroy = (id) => {
      if (confirm("Czy chcesz usunąć ten rekord?")) {
        Inertia.delete(route('clients.destroy', id))
      }
    }
    return {destroy}
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
    }
  }
}
</script>

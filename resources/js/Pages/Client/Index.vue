<template>
  <Head title="Kontrahenci"/>

  <breeze-authenticated-layout>
    <template #header>
      <h2 class="h4 font-weight-bold">
        Kontrahenci
      </h2>
      <div class="row">
        <div class="col">
          <Link :href="route('clients.create')" type="button" class="ml-5 btn btn-primary">
            Dodaj Kontrahenta
          </Link>
        </div>
        <div class="col">
          <div class="mb-3 row">
            <label for="search" class="col-sm-4 col-form-label">Wyszukiwarka</label>
            <div class="col-sm-8">
              <input type="text"
                     class="form-control"
                     id="search"
                     placeholder="Po prostu zacznij pisać..."
                     v-model="search"
              >
            </div>
          </div>
        </div>
        <div class="col-2">
          <select class="form-select" v-model="clientStatus">
            <option :value="{ status: 0 }">Aktywne</option>
            <option :value="{ status: 1 }">Archiwalne</option>
            <option :value="{ status: 2 }">Wszystkie</option>
          </select>
        </div>
      </div>
    </template>

    <div class="card shadow-sm">
      <div class="card-body">
        <table class="table">
          <thead>
          <tr>
            <th scope="col">Nazwa</th>
            <th scope="col">Adres</th>
            <th scope="col">Miasto</th>
            <th scope="col">NIP</th>
            <th scope="col">Telefon</th>
            <th scope="col">E-mail</th>
            <th scope="col">Archiwizacja</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td>{{ client.name }}</td>
            <td>{{ address(client.address, client.address2) }}</td>
            <td>{{ zipCity(client.zip, client.city) }}</td>
            <td>{{ client.nip }}</td>
            <td>{{ client.phone }}</td>
            <td>{{ client.mail }}</td>
            <td>{{ client.type }}</td>
            <td>
              <div class="btn-group btn-group-sm" role="group" aria-label="CRUD">
                <Link :href="route('clients.edit', client.id)"
                      type="button"
                      class="btn btn-success"
                >
                  Edycja
                </Link>
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
    </div>
  </breeze-authenticated-layout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head, Link} from '@inertiajs/inertia-vue3'
import {Inertia} from '@inertiajs/inertia'

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link
  },
  props: {
    clients: Object
  },
  data () {
    return {
      clientStatus: { status: 0 },
      search: ''
    }
  },
  setup() {
    const destroy = (id) => {
      if (confirm("Are you sure?")) {
        Inertia.delete(route('permissions.destroy', id))
      }
    }
    return {destroy}
  },
  methods: {
    zipCity(zip, city) {
      return zip + ' ' + city
    },
    address(address, address2) {
      let adr = address + ' ' + address2
      return adr.trim()
    }
  }
}
</script>

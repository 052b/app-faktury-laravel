<template>
  <Head title="Edycja Kontrahenta"/>

  <authenticated-layout>
    <template #header>
      <h2 class="h4 font-weight-bold">
        Kontrahenci
      </h2>
    </template>
    <div class="row">
      <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 col-xxl-4">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{ getTitle }}</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="form-group mb-2">
                <label class="form-label required">NIP</label>
                <div class="input-group mb-2">
                  <input type="text" class="form-control" :class="{'is-invalid': errors.nip}" name="nip"
                         v-model="form.nip">
                  <button class="btn" @click.prevent="onGetGusData">Pobierz z GUS</button>
                  <div v-if="errors.nip" class="invalid-feedback">{{ errors.nip }}</div>
                </div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label required">Nazwa kontrahenta</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.name}" name="name"
                       v-model="form.name">
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Adres</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.address}" name="address"
                       v-model="form.address">
                <div v-if="errors.address" class="invalid-feedback">{{ errors.address }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Adres c.d.</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.address2}" name="address2"
                       v-model="form.address2">
                <div v-if="errors.address2" class="invalid-feedback">{{ errors.address2 }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Kod pocztowy</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.zip}" name="zip"
                       v-model="form.zip">
                <div v-if="errors.zip" class="invalid-feedback">{{ errors.zip }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Miejscowość</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.city}" name="city"
                       v-model="form.city">
                <div v-if="errors.city" class="invalid-feedback">{{ errors.city }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Telefon</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.phone}" name="phone"
                       v-model="form.phone">
                <div v-if="errors.phone" class="invalid-feedback">{{ errors.phone }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">E-mail</label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.mail}" name="mail"
                       v-model="form.mail">
                <div v-if="errors.mail" class="invalid-feedback">{{ errors.mail }}</div>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Typ</label>
                <VueMultiselect v-model="selectedClientType"
                                track-by="id"
                                label="name"
                                placeholder="Wybierz"
                                :options="clientType"
                                :searchable="false"
                                :allow-empty="false"
                                :show-labels="false"
                                @update:model-value="onClientType"
                >
                </VueMultiselect>
                <div v-if="errors.type" class="invalid-feedback">{{ errors.type }}</div>
              </div>
              <div class="card-footer text-end">
                <div class="d-flex">
                  <Link :href="route('clients.index')" class="btn btn-link">Anuluj</Link>
                  <button type="submit" :disabled="form.processing" class="btn btn-primary ms-auto">
                    Zapisz
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </authenticated-layout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head, Link} from '@inertiajs/inertia-vue3'
import {reactive} from 'vue'
import {Inertia} from '@inertiajs/inertia'
import VueMultiselect from 'vue-multiselect'
import {findIndex} from "lodash";

export default {
  components: {
    AuthenticatedLayout,
    Head,
    Link,
    VueMultiselect
  },
  props: {
    client: Object,
    errors: Object,
  },
  setup() {
    const form = reactive({
      id: null,
      nip: '',
      name: '',
      address: '',
      address2: '',
      zip: '',
      city: '',
      phone: '',
      mail: '',
      type: 0
    })

    function submit() {
      if (form.id === null) {
        Inertia.post(route('clients.store'), form)
      } else {
        Inertia.put(route('clients.update', form.id), form)
      }
    }

    return {form, submit}
  },
  data() {
    return {
      clientType: [
        {id: 0, name: 'Tylko sprzedaż'},
        {id: 1, name: 'Tylko zakupy'},
        {id: 2, name: 'Dwustronna'},
      ],
      selectedClientType: null,
    }
  },
  mounted() {
    let clientTypeIdx = 0

    if (this.client.id) {
      this.form.id = this.client.id
      this.form.nip = this.client.nip
      this.form.name = this.client.name
      this.form.address = this.client.address
      this.form.address2 = this.client.address2
      this.form.zip = this.client.zip
      this.form.city = this.client.city
      this.form.phone = this.client.phone
      this.form.mail = this.client.mail
      this.form.type = this.client.type

      clientTypeIdx = findIndex(this.clientType, {'id': parseInt(this.client.type)})
    }
    this.selectedClientType = this.clientType[clientTypeIdx]
  },
  computed: {
    getTitle() {
      if (this.form.id) {
        return 'Edycja danych'
      }
      return 'Nowy rekord'
    }
  },
  methods: {
    onGetGusData() {
      if (this.form.nip) {
        const data = {
          nip: this.form.nip
        }

        axios.post(route('client.gus'), data)
            .then(response => {
              this.form.name = response.data.name
              this.form.address = response.data.address
              this.form.address2 = response.data.address2
              this.form.zip = response.data.zip
              this.form.city = response.data.city
            })
            .catch(error => {
              console.log(error)
            })
      }
    },
    onClientType(value) {
      this.form.type = value.id
    }
  }
}
</script>

<template>
  <Head title="Edycja Kontrahenta"/>

  <breeze-authenticated-layout>
    <template #header>
      <h2 class="h4 font-weight-bold">
        Edycja Kontrahenta
      </h2>

    </template>

    <div class="card shadow-sm">
      <div class="card-body">
        <form @submit.prevent="form.put(route('clients.update', form.id))">
          <div class="row mb-4">
            <div class="col">
              <div class="form-outline">
                <label class="form-label" for="client_name">Nazwa</label>
                <input type="text" name="client_name" id="client_name" class="form-control"
                       v-model="form.name"/>
                <div class="text-danger">{{ errors.name }}</div>
              </div>
            </div>
          </div>
          <!-- Submit button -->
          <button type="submit"
                  :disabled="form.processing"
                  class="btn btn-primary btn-block my-4"
          >
            Zapisz
          </button>

        </form>
      </div>
    </div>
  </breeze-authenticated-layout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head, Link, useForm} from '@inertiajs/inertia-vue3'

export default {
  components: {
    BreezeAuthenticatedLayout,
    Head,
    Link
  },
  props: {
    client: Object,
    errors: Object
  },
  setup(props) {
    const form = useForm({
      id: props.client.id,
      name: props.client.name,
    })
    return {form}
  }
}
</script>

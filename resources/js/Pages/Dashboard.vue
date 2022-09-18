<template>
  <Head title="Dashboard"/>

  <authenticated-layout>
    <template #header>
      <h2 class="h4 font-weight-bold">
        Dashboard
      </h2>
    </template>

    <div class="row row-cards">
      <div class="col-lg-6">
        <overdue-table :overdue="overdue" :title="'Przeterminowane'"></overdue-table>
      </div>
      <div class="col-lg-6">
        <div class="row row-cards">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div v-for="(values, name) in totals" class="mb-2">
                  <h2>{{ name }}</h2>
                  <div v-for="value in values">
                    <div>{{value}}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12">
            <overdue-table :overdue="overdueNext" :title="'Kończące się w ciągu 7 dni'" :pagination="false"></overdue-table>
          </div>
        </div>
      </div>
    </div>
    <date-paid-modal title="Data płatności" btn-cancel="Anuluj" btn-ok="Potwierdź"></date-paid-modal>

  </authenticated-layout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {Head} from '@inertiajs/inertia-vue3'
import OverdueTable from "@/Components/Invoice/OverdueTable";
import DatePaidModal from "@/Components/Invoice/DatePaidModal.vue"

export default {
  components: {
    OverdueTable,
    AuthenticatedLayout,
    Head,
    DatePaidModal
  },
  props: {
    auth: Object,
    roles: Array,
    permissions: Array,
    overdue: Object,
    overdueNext: Object,
    totals: Object
  }
}
</script>

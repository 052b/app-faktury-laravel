<template>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{ title }}</h3>
    </div>
    <div class="card-body border-bottom">
      <div class="table-responsive">
        <table class="table card-table table-vcenter datatable">
          <thead>
          <tr>
            <th>Numer</th>
            <th>Nabywca</th>
            <th>Kwota netto</th>
            <th>Zapłacono ?</th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="i in records" :key="i.id">
              <td>{{ i.invoice_number }}</td>
              <td>{{ i.name }}</td>
              <td>{{ i.sum_netto }}</td>
              <td><input class="form-check-input" type="checkbox" @click="onPaid(i.id)"></td>
            </tr>
          </tbody>
        </table>
        <TableEmpty v-if="records.length === 0"></TableEmpty>
      </div>
      <div v-if="pagination" class="card-footer d-flex align-items-center">
        <page-of-pages :from="overdue.from" :to="overdue.to" :total="overdue.total"></page-of-pages>
        <paginate :model-value="overdue.current_page"
                  :path="overdue.path"
                  :page-count="overdue.last_page"
                  :margin-pages="2"
                  :page-range="5"
                  container-class="pagination m-0 ms-auto"
                  prev-text="Poprzednia"
                  next-text="Następna"
        ></paginate>
      </div>
    </div>
  </div>
</template>

<script>
import PageOfPages from "@/Components/PageOfPages";
import Paginate from "@/Components/Paginate";
import {mapActions} from "pinia"
import {useStore} from "@/store/store"
import TableEmpty from "@/Components/TableEmpty";

export default {
  name: "OverdueTable",
  props: {
    overdue: Object,
    title: String,
    pagination: {
      type: Boolean,
      default: true
    }
  },
  components: {
    PageOfPages,
    Paginate,
    TableEmpty
  },
  computed: {
    records() {
      if (this.pagination) {
        return this.overdue.data
      }

      return this.overdue
    }
  },
  methods: {
    ...mapActions(useStore, ['openModal']),
    onPaid(id) {
      this.openModal(id)
    }
  }
}
</script>

<style scoped>

</style>

<template>
  <div class="modal modal-blur fade show" tabindex="-1"
       :class="{'d-block': showDatePayModal}"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button type="button" class="btn-close" aria-label="Close" @click="onCancel"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label class="form-label required">Wybierz datę</label>
            <date-picker v-model:value="datePaid"
                         type="date"
                         value-type="date"
                         clearable="false"
                         class="form-control"
                         :class="{'is-invalid': errors.datePaid}"
            ></date-picker>
            <div v-if="errors.datePaid" class="invalid-feedback">{{ errors.datePaid.pop() }}</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" @click="onCancel">{{ btnCancel }}</button>
          <button type="button" class="btn btn-primary" @click="onOk">{{ btnOk }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {useStore} from "@/store/store";
import {mapActions, mapState} from 'pinia'
import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import 'vue-datepicker-next/locale/pl';

export default {
  name: "DatePaidModal",
  props: {
    title: String,
    btnCancel: String,
    btnOk: String,
    show: Boolean
  },
  components: {
    DatePicker
  },
  data() {
    return {
      datePaid: new Date(),
      errors: {}
    }
  },
  computed: {
    ...mapState(useStore, ['showDatePayModal', 'getInvoiceId'])
  },
  methods: {
    ...mapActions(useStore, ['closeModal']),
    onCancel() {
      this.closeModal()
    },
    onOk() {
      const selectedDate = this.datePaid
      const data = {
        id: this.getInvoiceId,
        datePaid: selectedDate.getFullYear() + '-' + (selectedDate.getMonth() + 1) + '-' + selectedDate.getDate()
      }

      const self = this

      axios.post(route('invoices.set-paid'), data)
          .then(() => {
            self.closeModal()
            self.$inertia.reload()
          })
          .catch((error) => {
            if(error.response.status === 422) {
              self.errors = error.response.data.errors
            }
          })
    }
  }
}
</script>

<style scoped>

</style>

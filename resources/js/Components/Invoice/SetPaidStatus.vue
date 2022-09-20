<template>
  <VueMultiselect v-model="selectedStatus"
                  track-by="id"
                  label="name"
                  :options="options"
                  :searchable="false"
                  :allow-empty="false"
                  :show-labels="false"
                  @select="onSelect"
  >
  </VueMultiselect>
</template>

<script>
import VueMultiselect from 'vue-multiselect'
import {findIndex} from 'lodash'
import {mapActions} from "pinia"
import {useStore} from "@/store/store"

export default {
  name: "SetPaidStatus",
  props: {
    invoiceId: {
      type: Number,
      required: true
    },
    invoiceStatus: {
      type: Boolean,
      required: true
    }
  },
  components: {
    VueMultiselect
  },
  data() {
    return {
      selectedStatus: null,
      options: [
        {id: 0, name: 'Nieopłacona'},
        {id: 1, name: 'Opłacona'}
      ]
    }
  },
  mounted() {
    const idx = findIndex(this.options, {'id': this.invoiceStatus ? 1 : 0})
    this.selectedStatus = this.options[idx]
  },
  methods: {
    ...mapActions(useStore, ['openModal']),
    onSelect(option) {
      if(this.invoiceStatus) {
        axios.post(route('invoices.set-unpaid'), {id: this.invoiceId})
            .then(() => {
              console.log('OK')
            })
            .catch((error) => {
              if(error.response.status === 422) {
                console.log(error)
              }
            })
      } else {
        this.openModal(this.invoiceId)
      }
    }
  }
}
</script>

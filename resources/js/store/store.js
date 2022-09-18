import { defineStore } from 'pinia'

export const useStore = defineStore('main', {
  state: () => ({
    showDatePayModal: false,
    invoiceId: null
  }),
  getters: {
    getInvoiceId: (state) => {
      return state.invoiceId
    },
  },
  actions: {
    closeModal() {
      this.showDatePayModal = false
      this.invoiceId = null
    },
    openModal(id) {
      this.showDatePayModal = true
      this.invoiceId = id
    },
  },
})

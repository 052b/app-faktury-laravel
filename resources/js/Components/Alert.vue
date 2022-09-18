<template>
  <transition name="slide-fade">
    <div v-if="toast && visible && !popstate"
         class="alert alert-important alert-dismissible position-absolute top-0 end-0 mt-2 me-2 fade show"
         :class="alertType"
         role="alert"
         style="z-index: 1090"
    >
      <div class="d-flex">
        <div class="me-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24"
               viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
               stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 12l5 5l10 -10"></path>
          </svg>
          <!-- Download SVG icon from http://tabler-icons.io/i/check -->
          <!-- SVG icon code with class="alert-icon" -->
        </div>
        <div>
          {{ toast.message }}
        </div>
      </div>
      <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
  </transition>
</template>

<script>
export default {
  name: "Alert",
  props: {
    toast: Object,
    popstate: Boolean
  },
  data() {
    return {
      visible: false,
      timeout: null
    }
  },
  watch: {
    toast: {
      deep: true,
      immediate: true,
      handler() {
        this.visible = true

        if(this.timeout) {
          clearTimeout(this.timeout)
        }

        this.timeout = setTimeout(() => this.visible = false, 1000)
      }
    }
  },
  mounted() {
    console.log(this.popstate)
  },
  computed: {
    alertType() {
      if(this.toast) {
        switch (this.toast.type) {
          case 'success':
            return 'alert-success'
        }
      }

      return ''
    }
  }
}
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(50px);
  opacity: 0;
}
</style>

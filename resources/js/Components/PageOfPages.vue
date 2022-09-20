<template>
  <p class="m-0 text-muted"><span>{{ from }}</span> - <span>{{ to }}</span> / <span>{{ total }}</span></p>
</template>

<script>
export default {
  name: "PageOfPages",
  props: {
    from: {
      required: true
    },
    to: {
      required: true
    },
    total: {
      required: true
    }
  }
}
</script>

<style scoped>

</style>

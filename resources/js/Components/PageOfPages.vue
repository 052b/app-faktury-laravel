<template>
  <p class="m-0 text-muted"><span>{{ from }}</span> - <span>{{ to }}</span> / <span>{{ total }}</span></p>
</template>

<script>
export default {
  name: "PageOfPages",
  props: {
    from: {
      type: Number,
      required: true
    },
    to: {
      type: Number,
      required: true
    },
    total: {
      type: Number,
      required: true
    }
  }
}
</script>

<style scoped>

</style>

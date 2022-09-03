<template>
  <li :class="classes">
    <Link :href="href" class="nav-link">
      <span class="nav-link-title">
        <slot></slot>
      </span>
    </Link>
  </li>
</template>

<script>
import {defineComponent} from 'vue'
import {Link} from '@inertiajs/inertia-vue3'

export default defineComponent({
  components: {
    Link,
  },
  props: ['href', 'active'],
  computed: {
    classes () {
      return this.active
          ? 'nav-item active'
          : 'nav-item'
    }
  }
})
</script>

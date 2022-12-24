<template>
  <Head :title="title ? `${title}` : ''">
    <slot />
  </Head>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'

export default {
  components: {
    Head,
  },
  props: {
    title: String,
  },
}
</script>
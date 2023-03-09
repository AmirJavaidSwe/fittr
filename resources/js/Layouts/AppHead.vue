<script>
import { Head } from '@inertiajs/vue3';

export default {
  components: {
    Head,
  },
  props: {
    title: String,
  },
}
</script>

<template>
  <Head :title="title ? `${title}` : ''">
    <slot />
  </Head>
</template>
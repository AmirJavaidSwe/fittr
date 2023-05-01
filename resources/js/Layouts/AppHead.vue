<script>
import { Head } from '@inertiajs/vue3';

export default {
  components: {
    Head,
  },
  props: {
    title: String,
    favicon_type: String,
    favicon_image_url: String,
  },
}
</script>

<template>
  <Head :title="title ? `${title}` : ''">
    <link v-if="favicon_image_url" rel="icon" :type="favicon_type" :href="favicon_image_url" />
    <slot />
  </Head>
</template>
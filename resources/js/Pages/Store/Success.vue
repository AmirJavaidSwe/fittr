<script setup>
import { ref } from "vue";
import { router, usePage } from '@inertiajs/vue3'
import Section from '@/Components/Section.vue';

const subdomain = usePage().props.business_settings.subdomain;
const timer = ref(3);
const interval = setInterval(() => {
  if (timer.value === 0) {
    clearInterval(interval);
    router.get(route('ss.member.orders.index', { subdomain }))
  } else {
    timer.value--;
  }
}, 1000);
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl text-center">
            Success!<br>
            Redirecting in {{timer}}
        </div>
    </Section>
</template>

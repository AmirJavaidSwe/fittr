<script>
import AppLayout from '@/Layouts/AppLayout.vue'; 
export default {
    // Using a render function
    layout: (h, page) => h(AppLayout, () => page),
}
</script>
<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
</script>

<template>
    <SectionTitle>
        <template #title>
            {{$page.props.parner.name}}
        </template>
    </SectionTitle>
    <Section>
         {{$page.props.parner.email}}
    </Section>
</template>

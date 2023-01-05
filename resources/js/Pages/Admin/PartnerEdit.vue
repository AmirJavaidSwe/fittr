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

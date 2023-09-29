<script setup>
import Section from '@/Components/Section.vue';

import InstructorCard from './InstructorCard.vue';
import InstructorClassTypeFilters from './InstructorClassTypeFilters.vue';

const props = defineProps({
    instructors: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
    <div class="mx-20">
        <div class="mt-6 mb-6">
            <p class="text-3xl  text-center  font-bold">Instructors</p>
            <p class="text-center text-lg mb-6 font-bold">
                Getting you powerd up
            </p>
        </div>
        <InstructorClassTypeFilters />
        <div
        class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mx-32"
    >

        <template v-for="(instructor, index) in instructors" :key="index">
            <InstructorCard :instructor="instructor" />

        </template>
        </div>
    </div>
    </Section>
</template>


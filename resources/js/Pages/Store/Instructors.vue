<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import InstructorNavMenu from '@/Layouts/InstructorNavMenu.vue';
import { router, Link, usePage } from '@inertiajs/vue3';
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
        <InstructorNavMenu></InstructorNavMenu>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4">
            <div v-for="item in instructors" :key="item.id" class=" flex-grow">
                <img src="/images/gymwoman.jpg" alt="Instructor's Image" class="w-[300px] h-[250px] mb-2">
                <div class="font-bold mb-2">{{item.full_name}}</div>
                <div>CYCLE</div>
            </div>
        </div>
    </div>
    </Section>
</template>


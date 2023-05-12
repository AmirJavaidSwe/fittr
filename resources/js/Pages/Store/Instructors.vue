<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    instructors: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">
            Our Instructors
        </div>
        <div class="flex flex-wrap gap-4 flex-1">
            <div v-for="item in instructors" :key="item.id" class="bg-white p-4 rounded shadow-md flex-grow">
                <div class="font-bold mb-2">{{item.name}}</div>
                <div>email: {{item.email}}</div>
                <div>since: {{item.created_at}}</div>
            </div>
        </div>

    </Section>
</template>

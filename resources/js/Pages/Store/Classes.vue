<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    classes: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">
            Active classes list
        </div>
        <div class="flex flex-wrap gap-4 flex-1">
            <div v-for="item in classes" :key="item.id" class="bg-white p-4 rounded shadow-md flex-grow">
                <div class="font-bold mb-2">{{item.title}}</div>
                <div>start_date: {{item.start_date}}</div>
                <div>duration: {{item.duration}} minutes</div>
                <div>class type: {{item.class_type?.title}}</div>
                <div>instructor: {{item.instructor?.name}}</div>
                <div>is_off_peak: {{item.is_off_peak}}</div>
                <div>studio: {{item.studio?.title}}</div>
            </div>
        </div>

    </Section>
</template>

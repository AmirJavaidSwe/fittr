<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    class_packs: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">
            Class packs
        </div>
        <div class="flex flex-wrap gap-4 flex-1">
            <div v-for="item in class_packs" :key="item.id" class="bg-white p-4 rounded shadow-md flex-grow">
                <div class="font-bold mb-2">{{item.title}}</div>
                <div>type: {{item.type}}</div>
                <div>sessions: {{item.sessions}}</div>
                <div>price: {{item.price}}</div>
            </div>
        </div>

    </Section>
</template>

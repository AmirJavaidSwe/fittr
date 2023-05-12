<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    memberships: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">
            Memberships
        </div>
        <div class="flex flex-wrap gap-4 flex-1">
            <div v-for="item in memberships" :key="item.id" class="bg-white p-4 rounded shadow-md flex-grow">
                <div class="font-bold mb-2">{{item.title}}</div>
            </div>
        </div>

    </Section>
</template>

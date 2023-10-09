<script setup>
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    classtypes: {
        type: Object,
        required: true,
    },
    active_id: {
        type: Number
    }
});
const emit = defineEmits(["filter"]);
</script>

<template>
    <div class="flex justify-center my-7">
        <div class="overflow-x-auto">
            <ul class="flex flex-nowrap gap-2 mb-4">
                <template v-for="(item, index) in classtypes" :key="index">
                    <li class="">
                        <ButtonLink
                            styling="blank"
                            size="small"
                            :class="{'bg-primary-900 text-white hover:text-white': active_id === item.id}"
                            @click="$emit('filter', item.id)"
                        >
                            {{ item.title }}
                        </ButtonLink>
                        <div class="nav-line"></div>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    links: Array,
    to: String,
    from: String,
    total: String,
    per_page: {
        type: Number,
        required: false,
        default: 10,
    },
});
const emit = defineEmits(["pp_changed"]);

const pp = ref(props.per_page);

const optionsPP = [
    { value: 5, text: "5" },
    { value: 10, text: "10" },
    { value: 25, text: "25" },
    { value: 50, text: "50" },
];
</script>
<template>
    <div
        class="flex flex-col md:flex-row items-center justify-center md:justify-end overflow-hidden mb-10 lg:mb-0"
    >
        <div class="flex items-center mb-5 md:mb-0">
            <p class="mr-2 text-sm">Rows per page:</p>
            <select
                v-model="pp"
                @change="$emit('pp_changed', pp)"
                class="rounded mr-1 py-1"
            >
                <option
                    v-for="option in optionsPP"
                    v-bind:selected="option.value === props.per_page"
                    :value="option.value"
                >
                    {{ option.text }}
                </option>
            </select>
            <label for="search" class="sr-only outline-none"> Search </label>
            <p class="p-2 text-sm">
                {{ props.from }} - {{ props.to }} of
                {{ props.total }}
            </p>
        </div>
        <div v-if="links.length > 3">
            <div class="flex flex-wrap">
                <template v-for="(link, key) in props.links">
                    <div
                        v-if="link.url === null"
                        :key="key"
                        class="mr-1 px-4 py-2 text-gray-400 text-sm border rounded"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :key="`link-${key}`"
                        class="mr-1 px-4 py-2 focus:text-indigo-500 text-sm hover:bg-white border focus:border-indigo-500 rounded"
                        :class="{ 'bg-white': link.active }"
                        :href="link.url"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

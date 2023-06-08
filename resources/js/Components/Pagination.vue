<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faChevronLeft,
    faChevronRight,
} from "@fortawesome/free-solid-svg-icons";

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
console.log(props.links);
</script>
<template>
    <div
        class="flex flex-col md:flex-row items-center justify-center md:justify-end overflow-hidden mb-10 lg:mb-0"
    >
        <div class="flex items-center mb-5 md:mb-0">
            <p class="mr-2 text-sm lg:text-20vw text-dark/80 font-medium">
                Rows per page:
            </p>
            <select
                v-model="pp"
                @change="$emit('pp_changed', pp)"
                class="rounded mr-1 py-1 pr-8 pl-1 bg-transparent border-0 focus:ring-0 focus:outline-none focus:shadow-none focus:border-0"
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
                <template v-for="(link, key, index) in props.links">
                    <Link
                        v-if="link.label.includes('Previous')"
                        :key="key"
                        :href="link.url"
                        class="px-4 py-2 text-dark text-sm lg:text-20vw"
                        :class="
                            link.url === null
                                ? 'pointer-events-none opacity-40'
                                : null
                        "
                    >
                        <font-awesome-icon :icon="faChevronLeft" />
                    </Link>
                    <Link
                        v-else-if="link.label.includes('Next')"
                        :href="link.url"
                        class="px-4 py-2 text-dark text-sm lg:text-20vw"
                        :class="
                            link.url === null
                                ? 'pointer-events-none opacity-40'
                                : null
                        "
                    >
                        <font-awesome-icon :icon="faChevronRight" />
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>

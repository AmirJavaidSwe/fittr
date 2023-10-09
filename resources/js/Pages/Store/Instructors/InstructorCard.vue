<script setup>
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import { faCamera } from "@fortawesome/free-solid-svg-icons";

const subdomain = ref(usePage().props.business_settings.subdomain);
const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});

const instructorImage = computed(() => {
    return props.instructor?.profile?.images?.length
        ? props.instructor?.profile?.images[0]?.url
        : null;
});
</script>
<template>
    <div class="flex flex-col items-center">
        <ButtonLink :styling="'transparent'" :href="route('ss.instructors.show', {id: instructor.id, subdomain: subdomain})">
            <img v-if="instructorImage" :src="instructorImage" alt="Instructor's Image" class="w-60 h-60 object-fit rounded" />
            <div v-else class="w-60 h-60 bg-gray-800 text-gray-400 flex items-center justify-center rounded">
                <div class="text-center">
                    <font-awesome-icon :icon="faCamera" />
                    <div>coming soon</div>
                </div>
            </div>
        </ButtonLink>
        <div class="font-bold my-2">
            <ButtonLink :styling="'transparent'" :href="route('ss.instructors.show', {id: instructor.id, subdomain: subdomain})">{{ instructor.full_name }}</ButtonLink>
        </div>
        <div v-if="instructor.class_types.length">
            <span
                v-if="instructor.class_types.map((c) => c.title).join(' / ').length > 20"
                v-tooltip="instructor.class_types.map((c) => c.title).join(' / ')"
            >
                {{ instructor.class_types.map((c) => c.title).join(" / ").substring(0, 20) }}...
            </span>
            <span v-else>
                {{ instructor.class_types.map((c) => c.title).join(" / ") }}
            </span>
        </div>
    </div>
</template>

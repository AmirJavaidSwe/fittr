<script setup>
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
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
        <Link
            class="text-indigo-600 hover:text-indigo-500 via-indigo-950 text-center w-auto"
            :href="
                route('ss.instructor.profile.show', {
                    id: instructor.id,
                    subdomain: subdomain,
                })
            "
        >
            <img
                :src="instructorImage"
                alt="Instructor's Image"
                class="w-60 h-60 object-fit"
            />
        </Link>
        <div class="font-bold my-2">
            <ButtonLink :styling="'transparent'"
                :href="
                    route('ss.instructor.profile.show', {
                        id: instructor.id,
                        subdomain: subdomain,
                    })
                "
                >{{ instructor.full_name }}
            </ButtonLink>
        </div>
        <div v-if="instructor.class_types.length">
            <span
                v-if="instructor.class_types.join(' / ').length > 20"
                v-tooltip="instructor.class_types.join(' / ')"
            >
                {{ instructor.class_types.join(" / ").substring(0, 20) }}...
            </span>
            <span v-else>
                {{ instructor.class_types.join(" / ") }}
            </span>
        </div>
    </div>
</template>

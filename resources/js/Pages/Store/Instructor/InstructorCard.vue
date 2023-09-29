<script setup>
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
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
            class="text-indigo-600 hover:text-indigo-500 via-indigo-950 text-center"
            :href="
                route('ss.instructor.profile.show', {
                    id: instructor.id,
                    subdomain: subdomain,
                })
            "
            :data="{ id: instructor.id }"
        >
            <img
                :src="instructorImage"
                alt="Instructor's Image"
                class="w-full h-full object-cover"
            />
        </Link>
        <div class="font-bold mb-2">{{ instructor.full_name }}</div>
        <div>CYCLE</div>
    </div>
</template>

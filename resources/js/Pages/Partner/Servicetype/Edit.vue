<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    servicetype: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    id: props.servicetype.id,
    status: props.servicetype.status,
    title: props.servicetype.title,
    description: props.servicetype.description,
    image: props.servicetype.images?.length ? { ...props.servicetype.images[0] } : null,
});

const storeItem = () => {
    form
        .transform((data) => ({
            ...data,
            old_image: (data.image instanceof File) === false && !!form.image?.filename,
            _method: "put",
        }))
        .post(route("partner.servicetypes.update", form), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="w-full lg:w-1/2">
        <Form :form="form"
            :submitted="storeItem"
            :statuses="statuses"
            />
    </div>
</template>

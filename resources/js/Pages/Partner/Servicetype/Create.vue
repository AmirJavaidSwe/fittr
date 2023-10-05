<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";
defineProps({
    statuses: {
        type: Array,
        required: true,
    },
});

const storeItem = () => {
    form.post(route('partner.servicetypes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const form = useForm({
    status: false,
    title: null,
    description: null,
    image: null
});

</script>

<template>
    <div class="w-full lg:w-1/2">
        <Form :form="form"
            :submitted="storeItem"
            :statuses="statuses"
            />
    </div>
</template>

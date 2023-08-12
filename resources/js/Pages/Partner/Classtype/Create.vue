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
    form.post(route('partner.classtypes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const form = useForm({
    status: false,
    title: null,
    description: null
});

</script>

<template>
    <Form :form="form"
          :submitted="storeItem"
          :statuses="statuses"
          />
</template>

<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeItem = () => {
    form.post(route('partner.servicetypes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const form = useForm({
    title: null,
    description: null
});

</script>

<template>
    <Form :form="form"
          :submitted="storeItem"/>
</template>

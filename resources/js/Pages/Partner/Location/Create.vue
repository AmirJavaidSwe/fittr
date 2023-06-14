<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeStudio = () => {
    form.post(route('partner.studios.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

let form = useForm({
    title: null,
    ordering: null
});

</script>

<template>
    <Form :form="form"
          :submitted="storeStudio"/>
</template>
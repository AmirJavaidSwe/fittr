<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

defineProps({
    locations: Array
});

const storeStudio = () => {
    form.post(route('partner.studios.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

let form = useForm({
    title: null,
    location_id: null,
    // ordering: null
});

</script>

<template>
    <Form :form="form" :locations="locations"
          :submitted="storeStudio"/>
</template>
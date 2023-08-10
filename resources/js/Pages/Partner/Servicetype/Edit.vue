<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    servicetype: {
        type: Object,
        required: true
    }
});

const form = useForm({
    title: props.servicetype.title,
    description: props.servicetype.description
});

const storeItem = () => {
    form.put(route('partner.servicetypes.update', props.servicetype), {
        preserveScroll: true,
    });
};

</script>

<template>
    <Form :form="form"
          :submitted="storeItem"/>
</template>

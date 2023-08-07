<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeStudio = () => {
    form.put(route('partner.studios.update', props.studio), {
        preserveScroll: true,
        // onSuccess: () => form.reset()
    });
};

let props = defineProps({
    studio: {
        type: Object,
        required: true
    },
    locations: {
        type: Array,
        required: true
    },
    class_types: {
        type: Array,
        required: true
    },
});

let form = useForm({
    id: props.studio.id,
    title: props.studio.title,
    location_id: props.studio.location_id,
});

</script>

<template>
    <Form :form="form" :locations="locations"
          :class_types="class_types"
          :submitted="storeStudio"/>
</template>
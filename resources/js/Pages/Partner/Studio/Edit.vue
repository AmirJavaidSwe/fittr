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
    }
});

let form = useForm({
    title: props.studio.title,
    location_id: props.studio.location_id,
    // ordering: props.studio.ordering
});

</script>

<template>
    <Form :form="form" :locations="locations"
          :submitted="storeStudio"/>
</template>
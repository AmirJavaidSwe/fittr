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
    }
});

let form = useForm({
    title: props.studio.title,
    ordering: props.studio.ordering
});

</script>

<template>
    <Form :form="form"
          :submitted="storeStudio"/>
</template>
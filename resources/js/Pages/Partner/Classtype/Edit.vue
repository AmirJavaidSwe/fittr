<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    classtype: {
        type: Object,
        required: true
    }
});

const form = useForm({
    title: props.classtype.title,
    description: props.classtype.description
});

const storeItem = () => {
    form.put(route('partner.classtypes.update', props.classtype), {
        preserveScroll: true,
    });
};

</script>

<template>
    <Form :form="form"
          :submitted="storeItem"/>
</template>

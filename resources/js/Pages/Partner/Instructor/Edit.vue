<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeInstructor = () => {
    form.put(route('partner.instructors.update', props.instructor), {
        preserveScroll: true,
        // onSuccess: () => form.reset()
    });
};

let props = defineProps({
    instructor: {
        type: Object,
        required: true
    }
});

let form = useForm({
    first_name: props.instructor.first_name,
    last_name: props.instructor.last_name,
    email: props.instructor.email,
    phone: props.instructor.phone
});

</script>

<template>
    <Form :form="form"
          :submitted="storeInstructor"/>
</template>
<script setup>

import Form from "../Instructor/Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeInstructor = () => {
    form.post(route('partner.instructors.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

let form = useForm({
    name: '',
    email: ''
});

</script>

<template>
    <Form :form="form"
          :submitted="storeInstructor"/>
</template>
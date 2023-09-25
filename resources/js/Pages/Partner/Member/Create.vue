<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeMember = () => {
    form.post(route('partner.members.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

let form = useForm({
    first_name: null,
    last_name: null,
    email: null
});

</script>

<template>
    <Form :form="form"
          :submitted="storeMember"/>
</template>
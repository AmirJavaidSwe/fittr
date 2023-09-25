<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeMember = () => {
    form.put(route('partner.members.update', props.member), {
        preserveScroll: true,
        // onSuccess: () => form.reset()
    });
};

let props = defineProps({
    member: {
        type: Object,
        required: true
    }
});

let form = useForm({
    first_name: props.member.first_name,
    last_name: props.member.last_name,
    email: props.member.email,
    phone: props.member.phone
});

</script>

<template>
    <Form :form="form"
          :submitted="storeMember"/>
</template>
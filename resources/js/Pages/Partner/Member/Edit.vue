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
    name: props.member.name,
    email: props.member.email
});

</script>

<template>
    <Form :form="form"
          :submitted="storeMember"/>
</template>
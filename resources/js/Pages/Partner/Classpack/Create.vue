<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeItem = () => {
    form.post(route('partner.classpacks.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

const props = defineProps({
    options_types: Object,
    options_periods: Object,
    classtypes: Object,
});


const form = useForm({
    title: null,
    sessions: null,
    is_expiring: false,
    expiration: null,
    expiration_period: null,
    is_active: false,
    price: null,
    type: null,
    is_renewable: true,
    is_intro: false,
    is_private: false,
    private_url: null,
    active_from: null,
    active_to: null,
    is_restricted: false,
    restrictions: null,
});


</script>

<template>
    <Form :form="form"
          :isNew="true"
          :options_types="options_types"
          :options_periods="options_periods"
          :classtypes="classtypes"
          :submitted="storeItem"/>
</template>
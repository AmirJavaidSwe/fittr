<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    options_types: Object,
    options_periods: Object,
    classtypes: Object,
    classpack: {
        type: Object,
        required: true
    }
});

const form = useForm({
    title: props.classpack.title,
    sessions: props.classpack.sessions,
    is_expiring: props.classpack.is_expiring,
    expiration: props.classpack.expiration,
    expiration_period: props.classpack.expiration_period,
    is_active: props.classpack.is_active,
    price: props.classpack.price,
    type: props.classpack.type,
    is_renewable: props.classpack.is_renewable,
    is_intro: props.classpack.is_intro,
    is_private: props.classpack.is_private,
    private_url: props.classpack.private_url,
    active_from: props.classpack.active_from,
    active_to: props.classpack.active_to,
    is_restricted: props.classpack.is_restricted,
    restrictions: props.classpack.restrictions,
});

const storeItem = () => {
    form.put(route('partner.classpacks.update', props.classpack), {
        preserveScroll: true,
    });
};

</script>

<template>
    <Form :form="form"
          :options_types="options_types"
          :options_periods="options_periods"
          :classtypes="classtypes"
          :submitted="storeItem"/>
</template>
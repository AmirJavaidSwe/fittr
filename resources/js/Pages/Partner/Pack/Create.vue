<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    business_seetings: Object,
    pack_types: Object,
    periods: Object,
    classtypes: Object,
    // servicetypes: Object,
});

const form = useForm({
    type: null,
    title: null,
    sub_title: null,
    description: null,
    is_active: false,
    is_restricted: false,
    is_unlimited: false,
    is_fap: false,
    is_private: false,
    restrictions: null,
    private_url: null,
    active_from: null,
    active_to: null,
});

const formPrice = useForm({
    type: 'one_time',
    is_active: false,
    sessions: null,
    is_expiring: false,
    expiration: null,
    expiration_period: null,
    interval_count: null,
    interval: null,
    is_ongoing: true,
    fixed_count: null,
    price: null,
    is_renewable: true,
    is_intro: false,
});

const storeItem = () => {
    form.post(route('partner.packs.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Form :form="form"
          :isNew="true"
          :formPrice="formPrice"
          :pack_types="pack_types"
          :periods="periods"
          :classtypes="classtypes"
          :default_currency="business_seetings.default_currency"
          :submitted="storeItem"/>
</template>
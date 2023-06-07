<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    business_seetings: Object,
    pack_types: Object,
    periods: Object,
    price_types: Object,
    classtypes: Object,
    pack: {
        type: Object,
        required: true
    }
});

const form = useForm({
    type: props.pack.type,
    title: props.pack.title,
    sub_title: props.pack.sub_title,
    description: props.pack.sub_title,
    is_active: props.pack.is_active,
    is_restricted: props.pack.is_restricted,
    is_unlimited: props.pack.is_unlimited,
    is_fap: props.pack.is_fap,
    is_private: props.pack.is_private,
    restrictions: props.pack.restrictions,
    private_url: props.pack.private_url,
    active_from: props.pack.active_from,
    active_to: props.pack.active_to,
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

const updateItem = () => {
    form.put(route('partner.packs.update', props.pack), {
        preserveScroll: true,
    });
};

const storePrice = () => {
    console.log(formPrice);
    formPrice.post(route('partner.packs.price.store', props.pack), {
        preserveScroll: true,
        onSuccess: () => formPrice.reset()
    });
};
const editPrice = (action, id) => {
    useForm({'action': action}).put(route('partner.packs.price.update', { pack: props.pack, price: id } ), {
        preserveScroll: true,
    });
};

</script>

<template>
    <Form :form="form"
          :isNew="false"
          :formPrice="formPrice"
          :pack_types="pack_types"
          :periods="periods"
          :price_types="price_types"
          :classtypes="classtypes"
          :default_currency="business_seetings.default_currency"
          :prices="pack.prices"
          :submitted="updateItem"
          :savePrice="storePrice"
          :editPrice="editPrice"
        />
</template>
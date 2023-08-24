<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    business_settings: Object,
    pack_types: Array,
    periods: Array,
    price_types: Array,
    locations: Array,
    classtypes: Object,
    servicetypes: Object,
    pack: {
        type: Object,
        required: true
    }
});

const form = useForm({
    type: props.pack.type,
    title: props.pack.title,
    sub_title: props.pack.sub_title,
    description: props.pack.description,
    is_active: props.pack.is_active,
    is_restricted: props.pack.is_restricted,
    is_private: props.pack.is_private,
    restrictions: props.pack.restrictions,
    private_url: props.pack.private_url,
    active_from: props.pack.active_from,
    active_to: props.pack.active_to,
});

const updateItem = () => {
    form.put(route('partner.packs.update', props.pack), {
        preserveScroll: true,
    });
};

const toggleOrDeletePrice = (action, id) => {
    useForm({'action': action}).put(route('partner.packs.price.update', { price: id } ), {
        preserveScroll: true,
    });
};

</script>

<template>
    <Form :form="form"
          :isNew="false"
          :pack_id="pack.id"
          :pack_types="pack_types"
          :periods="periods"
          :price_types="price_types"
          :locations="locations"
          :classtypes="classtypes"
          :servicetypes="servicetypes"
          :default_currency="business_settings.default_currency"
          :pack_prices="pack.pack_prices"
          :submitted="updateItem"
          :toggleOrDeletePrice="toggleOrDeletePrice"
        />
</template>
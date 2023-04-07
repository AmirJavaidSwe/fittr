<template>
    <Form :form="form"
          :submitted="storeAmenity"/>
</template>

<script setup>

import Form from "./Form.vue";
import {useForm} from '@inertiajs/vue3';

const storeAmenity = () => {
    form.post(route('partner.amenity.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

let form = useForm({
    title: '',
    icon: '',
    contents: '',
    ordering: '',
    studio_id: '',
    status: false
});

</script>

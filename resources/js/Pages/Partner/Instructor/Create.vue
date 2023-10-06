<script setup>

import Form from "../Instructor/Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeItem = () => {
    form.post(route('partner.instructors.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};

let form = useForm({
    first_name: null,
    last_name: null,
    email: null,
    phone: null,
    profile_description: null,
    profile_image: null,
    old_profile_image: false,
});

</script>

<template>
    <div class="w-full lg:w-1/2">
        <Form :form="form"
            :submitted="storeItem"/>
    </div>
</template>
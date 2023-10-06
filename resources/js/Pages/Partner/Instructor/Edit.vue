<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

let props = defineProps({
    instructor: {
        type: Object,
        required: true
    }
});

let form = useForm({
    id: props.instructor.id,
    first_name: props.instructor.first_name,
    last_name: props.instructor.last_name,
    email: props.instructor.email,
    phone: props.instructor.phone,
    profile_description: props.instructor.profile.description,
    profile_image: props.instructor.profile.images?.length ? { ...props.instructor.profile.images[0] } : null,
    old_profile_image: false,
});

const storeItem = () => {
    form
        .transform((data) => ({
            ...data,
            old_profile_image: (data.profile_image instanceof File) === false && !!form.profile_image?.filename,
            _method: "put",
        }))
        .post(route("partner.instructors.update", form.id), {
            preserveScroll: true,
        });
};

</script>

<template>
    <div class="w-full lg:w-1/2">
        <Form :form="form"
                :submitted="storeItem"/>
    </div>
</template>
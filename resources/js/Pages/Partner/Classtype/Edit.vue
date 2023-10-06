<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    classtype: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    id: props.classtype.id,
    status: props.classtype.status,
    title: props.classtype.title,
    description: props.classtype.description,
    image: props.classtype.images?.length ? { ...props.classtype.images[0] } : null,
});

const storeItem = () => {
    form
        .transform((data) => ({
            ...data,
            old_image: (data.image instanceof File) === false && !!form.image?.filename,
            _method: "put",
        }))
        .post(route("partner.classtypes.update", form), {
        preserveScroll: true,
    });
};

</script>

<template>
    <div class="w-full lg:w-1/2">
        <Form :form="form"
            :submitted="storeItem"
            :statuses="statuses"
            />
    </div>
</template>

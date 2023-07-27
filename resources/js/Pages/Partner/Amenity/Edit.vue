<template>
    <Form :form="form"
          :submitted="updateAmenity"/>
</template>

<script setup>
import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const updateAmenity = () => {
    form.put(route('partner.amenity.update', props.amenity), {
        preserveScroll: true,
    });
};

let props = defineProps({
    amenity: {
        type: Object,
        required: true
    }
})

let form = useForm({
    title: props.amenity?.title !== undefined ? props.amenity?.title : '',
    status: props.amenity?.status !== undefined ? props.amenity?.status : ''
});
</script>

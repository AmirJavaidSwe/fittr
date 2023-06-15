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
    icon: props.amenity?.icon !== undefined ? props.amenity?.icon : '',
    contents: props.amenity?.contents !== undefined ? props.amenity?.contents : '',
    ordering: props.amenity?.ordering !== undefined ? props.amenity?.ordering : '',
    status: props.amenity?.status !== undefined ? props.amenity?.status : ''
});
</script>

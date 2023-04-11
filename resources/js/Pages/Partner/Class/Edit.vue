<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeClass = () => {
    form.put(route('partner.classes.update', props.class_lesson), {
        preserveScroll: true,
    });
};

let props = defineProps({
    class_lesson: {
        type: Object,
        required: true
    },
    statuses: Object,
    studios: Object,
    instructors: Object,
});

let form = useForm({
    name: props.class_lesson.name,
    status: props.class_lesson.status,
    start_date: props.class_lesson.start_date,
    end_date: props.class_lesson.end_date,
    instructor_id: props.class_lesson.instructor_id,
    studio_id: props.class_lesson.studio_id,
    is_offpeak: props.class_lesson.is_offpeak,
});

</script>

<template>
    <Form :form="form"
        :statuses="statuses"
        :studios="studios"
        :instructors="instructors"
        :submitted="storeClass"
        />
</template>
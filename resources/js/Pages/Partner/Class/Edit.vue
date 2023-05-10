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
    classtypes: Object,
    business_seetings: Object,
});

let form = useForm({
    title: props.class_lesson.title,
    status: props.class_lesson.status,
    start_date: props.class_lesson.start_date,
    end_date: props.class_lesson.end_date,
    instructor_id: props.class_lesson.instructor_id,
    class_type_id: props.class_lesson.class_type_id,
    studio_id: props.class_lesson.studio_id,
    is_off_peak: props.class_lesson.is_off_peak,
});

</script>

<template>
    <Form :form="form"
        :statuses="statuses"
        :studios="studios"
        :instructors="instructors"
        :classtypes="classtypes"
        :business_seetings="business_seetings"
        :submitted="storeClass"
        />
</template>

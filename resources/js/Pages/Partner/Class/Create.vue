<script setup>

import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";

const storeClass = () => {
    form.post(route('partner.classes.store'), {
        preserveScroll: true,
    });
};

const props = defineProps({
    statuses: Object,
    studios: Object,
    instructors: Object,
    classtypes: Object,
    business_seetings: Object,
});

let form = useForm({
    title: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: null,
    class_type_id: null,
    studio_id: null,
    is_off_peak: false,
    does_repeat: false,
    repeat_end_date: null,
    week_days: []
});

</script>

<template>
    <Form
        :form="form"
        :isNew="true"
        :statuses="statuses"
        :studios="studios"
        :instructors="instructors"
        :classtypes="classtypes"
        :business_seetings="business_seetings"
        :submitted="storeClass"
          />
</template>

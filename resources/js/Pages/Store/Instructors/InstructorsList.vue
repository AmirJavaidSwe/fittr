<script setup>
import { ref, computed } from "vue";
import Section from "@/Components/Section.vue";
import InstructorCard from "./InstructorCard.vue";
import InstructorClassTypeFilters from "./InstructorClassTypeFilters.vue";

const props = defineProps({
    instructors: {
        type: Array,
        required: false,
    },
    class_types: {
        type: Array,
        required: false,
    },
});

const active_id = ref(0);
const filter = (class_type_id) => {
    console.log(class_type_id)
    active_id.value = class_type_id;
};

const filteredInstructors = computed(() => {
    //ALL:
    if(active_id.value === 0) return props.instructors;

    // Return a new array of instructors based on the filter condition
    return props.instructors.filter((instructor) => {
        return instructor.class_types.some((ct) => ct.id == active_id.value);
    });
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6">
                <p class="text-3xl text-center font-bold">Instructors</p>
                <p class="text-center text-lg mb-6 font-bold">
                    Getting you powered up
                </p>
            </div>

            <InstructorClassTypeFilters :classtypes="class_types" :active_id="active_id" @filter="filter" />

            <div class="flex flex-wrap justify-center gap-6">
                <template v-for="(instructor, index) in filteredInstructors" :key="index">
                    <InstructorCard :instructor="instructor" />
                </template>
            </div>
        </div>
    </Section>
</template>

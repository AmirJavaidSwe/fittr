<script setup>
import { ref, computed } from "vue";
import Section from "@/Components/Section.vue";
import InstructorCard from "./InstructorCard.vue";
import InstructorClassTypeFilters from "./InstructorClassTypeFilters.vue";

const props = defineProps({
    instructors: {
        type: Object,
        required: true,
    },
    class_types: {
        type: Object,
        required: true,
    },
});

// Move the filterBy ref outside of the filter() function
const filterBy = ref(false);

const runFilter = ($event) => {
    filterBy.value = $event;
};

const filteredInstructors = computed(() => {
    if(!filterBy.value) return props.instructors
    // Return a new array of instructors based on the filter condition
    return props.instructors.filter((instructor) => {
        // Access the class_types array for each instructor
        const classTypes = instructor.class_types.map((value) => value.toLowerCase());

        const singleClassType = props.class_types.find((o) => {
            return o.id === filterBy.value;
        });

        return classTypes.includes(singleClassType.title.toLowerCase());

    });
})

</script>

<template>
    <Section bg="bg-transparent">
        <div class="mx-20">
            <div class="mt-6 mb-6">
                <p class="text-3xl text-center font-bold">Instructors</p>
                <p class="text-center text-lg mb-6 font-bold">
                    Getting you powerd up
                </p>
            </div>
            <InstructorClassTypeFilters :classtypes="class_types" @filter="runFilter" />
            <div
                class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 2xl:mx-32"
            >
                <template
                    v-for="(instructor, index) in filteredInstructors"
                    :key="index"
                >
                    <InstructorCard :instructor="instructor" />
                </template>
            </div>
        </div>
    </Section>
</template>

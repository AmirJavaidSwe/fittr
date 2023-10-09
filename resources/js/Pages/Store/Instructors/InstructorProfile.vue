<script setup>
import { router } from '@inertiajs/vue3'
import { DateTime } from 'luxon';
import Section from "@/Components/Section.vue";
import ClassCard from "@/Pages/Store/Classes/ClassCard.vue";
import { faCamera } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
    classes: {
        type: Object,
        required: true,
    },
    business_settings: {
        Object,
        required: true,
    },
});
</script>
<template>
    <Section bg="bg-transparent mx-40">
        <div class="mx-auto max-w-6xl mt-8 p-4 gap-0 flex-grow-1">
            <div class="text-center">
                <h1 class="text-3xl font-bold">Instructors</h1>
                <p class="text-lg font-semibold">Getting you powered up</p>
            </div>

            <div class="flex flex-wrap md:flex-nowrap gap-8 justify-center my-8">
                <div class="md:flex-shrink-0 w-96">
                    <img v-if="props.instructor?.profile?.images[0]?.url" :src="props.instructor?.profile?.images[0]?.url" alt="Instructor's Image" class=" object-fit rounded" />
                    <div v-else class="w-full h-full bg-gray-800 text-gray-400 flex items-center justify-center rounded">
                        <div class="text-center">
                            <font-awesome-icon :icon="faCamera" />
                            <div>coming soon</div>
                        </div>
                    </div>
                </div>
                <div class="overflow-y-auto flex-grow space-y-2">
                    <div class="text-2xl font-semibold">
                        {{ instructor.full_name }}
                    </div>
                    <div v-if="instructor.class_types?.length" class="text-sm font-semibold">
                        {{ instructor.class_types.map((ct) => ct.title).join(" / ") }}
                    </div>
                    <div v-if="instructor?.profile?.description" class="text-gray-600 md:h-48 max-h-full">
                        {{ instructor?.profile?.description }}
                    </div>
                </div>
            </div>

            <div v-if="Object.keys(classes).length" class="my-12">
                <p class="text-2xl font-semibold mb-8">
                    Next sessions with {{ instructor.full_name }}
                </p>
                <div v-for="(classes, day) in classes">
                    <div class="font-bold bg-white text-gray-600 p-1 mb-2">{{DateTime.fromISO(day).setZone(business_settings.timezone).toFormat('cccc, ' + business_settings.date_format.format_js)}}</div>
                    <div class="flex flex-wrap gap-4 mb-4">
                        <ClassCard 
                            v-for="item in classes"
                            :item="item"
                            :business_settings="business_settings"
                            :key="item.id"
                            :class="{
                                'border border-primary-500 bg-gray-200': !item.on_waitlist && !item.is_booked,
                                'border border-black hover:border-primary-500 bg-white': !item.on_waitlist && !item.is_booked,
                                'shadow-md': item.is_booked || item.on_waitlist,
                            }"
                            :style="{
                                backgroundColor: item.is_booked ? 'rgba(41, 181, 128, 0.5)' : (item.on_waitlist ? 'rgba(232, 168, 56, 0.5)' : ''),
                            }"
                            class="w-72"
                            @click="router.visit(item.url)"
                            >
                        </ClassCard>
                    </div>
                </div>
            </div>
        </div>
    </Section>
</template>

<script setup>
import Avatar from '@/Components/Avatar.vue';
import SideModal from '@/Components/SideModal.vue';

defineProps({
    show: {
        required: true,
        type: Boolean,
        default: false,
    },
    classDetails: {
        required: true,
        type: Object,
    },
    business_settings: {
        required: true,
        type: Object,
    },
});

defineEmits(['close']);

</script>
<template>
    <SideModal :show="show" @close="$emit('close')">
        <template #title>Details</template>
        <template #content>
            <div class="flex text-3xl font-bold mb-4 items-center">
                <div class="flex-grow mr-4">{{ classDetails.title }}</div>
                <div class="flex flex-col shrink-0">
                    <div
                        v-if="classDetails.is_free"
                        class="inline-flex text-sm font-normal rounded-lg bg-green-500 text-white p-2 justify-center mb-2">
                        Free</div>
                    <div class="inline-flex text-sm font-normal rounded-lg text-white p-2 justify-center"
                        :class="{ 'bg-danger-600': !classDetails.spaces_left, 'bg-gray-500': classDetails.spaces_left }">
                        {{ classDetails.spaces_left > 0 ? 'Not Full' : 'Full' }}</div>
                </div>
            </div>

            <div v-if="classDetails.instructors?.length" class="flex items-center gap-1 mb-4" v-for="instructor in classDetails.instructors" :key="instructor.id">
                <Avatar
                    :initials="instructor.initials"
                    :imageUrl="instructor.profile_photo_url"
                    :useIcon="true"
                    size="medium"
                />
                {{instructor.full_name}}
            </div>
            
            <div class="flex mb-3">
                <div class="flex-grow">Class Type:</div>
                <div class="font-bold">
                    {{ classDetails.class_type?.title }}
                </div>
            </div>

            <div class="flex mb-3">
                <div class="flex-grow">Time:</div>
                <div class="font-bold">
                    {{ classDetails.start_date?.toFormat("hh:mm a") }} -
                    {{ classDetails.end_date?.toFormat("hh:mm a") }}
                </div>
            </div>

            <div class="flex mb-3">
                <div class="flex-grow">
                    Duration:
                </div>
                <div class="font-bold">
                    {{ classDetails.duration }} minutes
                </div>
            </div>

            <div class="flex mb-3">
                <div class="flex-grow">Date:</div>
                <div class="font-bold">
                    {{ classDetails.start_date?.toFormat(business_settings.date_format?.format_js) }}
                </div>
            </div>
        </template>
    </SideModal>
</template>

<script setup>
import AvatarValue from '@/Components/DataTable/AvatarValue.vue';
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
            <div class="w-full mb-4">
                <img class="w-full rounded-md" v-if="classDetails.studio?.location?.images?.length"
                    :src="classDetails.studio?.location?.images[0].url" :alt="classDetails.studio?.location?.images[0]
                            .original_filename
                        " />
            </div>
            <div class="flex flex-col">
                <div class="flex text-3xl font-bold mb-4 items-center">
                    <div class="flex flex-grow mr-4">{{ classDetails.title }}</div>
                    <div class="flex flex-col shrink-0">
                        <div
                            class="inline-flex text-sm font-normal rounded-lg bg-green-500 text-white p-2 justify-center mb-2">
                            Free</div>
                        <div class="inline-flex text-sm font-normal rounded-lg text-white p-2 justify-center"
                            :class="{ 'bg-danger-600': !classDetails.spaces_left, 'bg-gray-500': classDetails.spaces_left }">
                            {{ classDetails.spaces_left > 0 ? 'Not Full' : 'Full' }}</div>
                    </div>
                </div>

                <div class="flex flex-row mb-4">
                    <div class="flex flex-row">
                        <div class="flex mr-2">
                            <!-- <img src="" class="inline-block rounded-xl w-full h-full bg-gray-500" alt="User" /> -->
                            <template v-if="classDetails?.instructor?.length">
                                <template v-for="(
                                        instructor, ins
                                    ) in classDetails?.instructor" :key="ins">
                                    <AvatarValue
                                        class="cursor-pointer inline-flex justify-center mr-1 text-center items-center"
                                        :onlyTooltip="true" :title="instructor?.name ?? 'Demo Ins'" />
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Class Type:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.class_type?.title }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Time:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.start_date?.toFormat("hh:mm a") }} -
                        {{ classDetails.end_date?.toFormat("hh:mm a") }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Lession Time:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.duration }} minutes
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Date:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.start_date?.toFormat(business_settings.date_format?.format_js) }}
                    </div>
                </div>
            </div>
        </template>
    </SideModal>
</template>

<script setup>
import { DateTime } from 'luxon';
import Avatar from '@/Components/Avatar.vue';
import ClockIcon from '@/Icons/ClockIcon.vue';
import LocationIcon from '@/Icons/LocationIcon.vue';

const props = defineProps({
    item: {
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
    <div class="cursor-pointer relative rounded-md p-3 mb-3">
        <div class="flex justify-between items-center mb-4">
            <div class="text-2xl font-bold self-center" v-tooltip="DateTime.fromISO(item.start_date)
                .setZone(business_settings.timezone)
                .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format.format_js)
                ">
                {{
                    DateTime.fromISO(item.start_date)
                        .setZone(business_settings.timezone)
                        .toFormat(business_settings.time_format.format_js)
                }}
            </div>
            <div class="flex flex-row rounded-lg text-green-800 px-2 py-1 items-center justify-center"
                style="background: rgba(66, 112, 95, 0.2)">
                <div class="mr-1">
                    <ClockIcon />
                </div>
                <div class="text-sm font-semibold mt-[1px]">
                    {{ item.duration }}
                </div>
            </div>
        </div>
        <div class="mb-4 pt-4 border-t border-gray-300 items-center">

            <VTooltip v-if="item.title.length > 20" :triggers="['hover', 'click']">
                <div class="font-bold truncate">{{item.title}}</div>

                <template #popper>
                    <div class="w-40">{{item.title}}</div>
                </template>
            </VTooltip>
            <div v-else class="font-bold truncate">{{item.title}}</div>

            <div class="uppercase font-semibold">
                {{ item.class_type?.title }}
            </div>
        </div>
        <div v-if="item.instructors?.length" class="flex items-center gap-1" v-for="instructor in item.instructors" :key="instructor.id">
                <Avatar
                    :initials="instructor.initials"
                    :imageUrl="instructor?.profile?.images[0]?.url"
                    :useIcon="true"
                    size="xs"
                />
                {{instructor.full_name}}
        </div>
        <div v-if="!item.is_booked && !item.on_waitlist" class="border-t border-gray-300"></div>
        <div class="flex flex-wrap my-4 items-center">
            <div class="mr-2">
                <LocationIcon />
            </div>
            <div class="text-base text-green-700 uppercase">
                <span class="mt-[1px]">{{ item.studio?.location?.title }}</span>
            </div>
        </div>

        <div v-if="item.is_free" class="bg-primary-300 text-white rounded-lg px-2 py-1 font-bold text-center">FREE CLASS</div>

        <template v-if="item.is_booked">
            <div class="border-t border-gray-300"></div>
            <div class="flex mt-4 items-center justify-center text-xl text-white font-bold uppercase">
                Booked
            </div>
        </template>
        <template v-else-if="item.on_waitlist">
            <div class="border-t border-gray-300"></div>
            <div class="flex mt-4 items-center justify-center text-xl text-white font-bold uppercase">
                On Waitlist
            </div>
        </template>
    </div>
</template>
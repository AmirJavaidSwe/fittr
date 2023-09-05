<script setup>
import { computed } from 'vue';
import { DateTime } from 'luxon';
import { faEllipsis, faUserGroup, faPencil, faCopy, faTrashCan, faLock, faPowerOff, faMask, faCalendarDays } from '@fortawesome/free-solid-svg-icons';
import ButtonLink from '@/Components/ButtonLink.vue';
import DonutText from '@/Components/Charts/DonutText.vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    pack: {
        type: Object,
        required: true,
    },
    price_types: {
        type: Array,
        required: true,
    },
    business_settings: Object,
});

const donutPercent = computed(() => {
    if(props.pack.memberships_count === 0) return 0;
    const p =  (props.pack.active_memberships_count / props.pack.memberships_count) * 100;
    return Math.round(p);
});

defineEmits(['copy', 'toggle', 'delete']);

</script>

<template>
    <div 
        class="relative flex flex-col"
        :class="{
                'border-secondary-600': pack.type == 'class_lesson',
                'border-blue-600': pack.type == 'service',
                'border-secondary-100': pack.type == 'hybrid',
                'border-primary-400': pack.type == 'default',
                'border-cyan-500': pack.type == 'corporate',
            }">
        <div v-if="pack.type == 'hybrid'" class="absolute bg-gradient-to-r from-secondary-600 h-2 inset-x-0 top-[-8px] rounded-tl-md rounded-tr-md to-blue-600 w-full"></div>
        <Dropdown align="right" width="48" :content-classes="['bg-gray-100', 'p-1', 'space-y-4']" class="h-6 mx-2 place-self-end w-6" :class="{'z-10': !pack.is_active}">
            <template #trigger>
                <ButtonLink>
                    <font-awesome-icon :icon="faEllipsis" class="w-6 h-6" :class="{'text-white': !pack.is_active}" />
                </ButtonLink>
            </template>
            <template #content>
                <ButtonLink 
                    :href="route('partner.packs.edit', pack)"
                    size="small"
                    class="flex justify-between w-full"
                    styling="default"
                    >
                    Edit
                    <font-awesome-icon :icon="faPencil" />
                </ButtonLink>
                <ButtonLink 
                    @click="$emit('copy', pack.id)"
                    size="small"
                    class="flex justify-between w-full"
                    styling="secondary"
                    >
                    Duplicate
                    <font-awesome-icon :icon="faCopy" />
                </ButtonLink>
                <ButtonLink 
                    @click="$emit('toggle', pack.id)"
                    size="small"
                    class="flex justify-between w-full"
                    :styling="pack.is_active ? 'default' : 'primary'"
                    >
                    {{pack.is_active ? 'Deactive' : 'Make active'}} 
                    <font-awesome-icon :icon="faPowerOff" />
                </ButtonLink>
                <ButtonLink 
                    @click="$emit('delete', pack.id)"
                    size="small"
                    class="flex justify-between w-full"
                    styling="danger"
                    type="button"
                    >
                    Delete
                    <font-awesome-icon :icon="faTrashCan" />
                </ButtonLink>
            </template>
        </Dropdown>
        <div class="p-2 flex gap-4">
            <DonutText 
                    class="w-20 flex-shrink-0"
                    :percent="donutPercent"
                    :title="donutPercent+'%'"
                    :subtitle="'active'"
                >
            </DonutText>
            <div class="text-gray-500">
                <div class="font-bold text-xl text-black leading-tight mb-2">{{pack.title}}</div>
                <div class="mb-1 border-b">
                    <font-awesome-icon :icon="faUserGroup" class="mr-2" />
                    <span v-tooltip.bottom="'Pack total: ' + pack.memberships_count">
                        {{pack.active_memberships_count}} active
                    </span>
                </div>
                <div class="flex gap-4 items-center">
                    <font-awesome-icon
                        v-if="pack.is_restricted"
                        :icon="faLock"
                        v-tooltip="'Pack has restrictions'" />
                    <font-awesome-icon
                        v-if="pack.is_private"
                        :icon="faMask"
                        v-tooltip="'Pack is private'" />
                    <font-awesome-icon
                        v-if="pack.not_yet_active === true"
                        :icon="faCalendarDays"
                        class="text-red-500"
                        v-tooltip="'Pack will be available from '+
                        DateTime.fromISO(pack.active_from)
                        .setZone(business_settings.timezone)
                        .toFormat(business_settings.date_format.format_js)" />
                    <font-awesome-icon
                        v-if="pack.will_become_inactive === true"
                        :icon="faCalendarDays"
                        class="text-secondary-500"
                        v-tooltip="'Pack will be available until the end of '+
                        DateTime.fromISO(pack.active_to)
                        .setZone(business_settings.timezone)
                        .toFormat(business_settings.date_format.format_js)" />
                    <font-awesome-icon
                        v-if="pack.became_inactive === true"
                        :icon="faCalendarDays"
                        class="text-red-500"
                        v-tooltip="'Pack was available until the end of '+
                        DateTime.fromISO(pack.active_to)
                        .setZone(business_settings.timezone)
                        .toFormat(business_settings.date_format.format_js)" />
                </div>
            </div>
        </div>

        <div v-if="pack.sub_title || pack.description" class="p-2">
            <div v-tooltip.left="'pack.sub_title'" class="truncate">{{pack.sub_title}}</div>
            <div v-tooltip="pack.description" class="truncate">{{pack.description}}</div>
        </div>

        <div class="bg-gray-50 border border-gray-50 font-bold place-self-start px-2 rounded-tr-lg text-sm mt-2">Pricing options</div>

        <div class="bg-gray-50 px-2 py-4 space-y-2 flex-grow">
            <div v-if="pack.pack_prices.length" v-for="pack_price in pack.pack_prices" :key="pack_price.id" class="capitalize flex justify-between border-b last:border-none">
                <span>
                    <template v-if="pack_price.type == 'one_time'">
                    {{price_types.find(({value}) => value === pack_price.type)?.label}}
                    </template>
                    <template v-if="pack_price.type == 'recurring'">
                    {{pack_price.interval_adjective}}
                    </template>
                </span>
                <span class="font-bold">
                {{pack_price.price_formatted}}
                </span>
            </div>
            <div v-else>No options</div>
        </div>
        <div v-if="!pack.is_active" class="absolute bg-gradient-to-b from-black/40 to-black/90 inset-0 rounded-bl-md rounded-br-md">
            <div class="absolute bg-danger-700 bottom-0 rounded-bl-md rounded-br-md text-center text-white w-full">Inactive</div>
        </div>
    </div>
</template>
<script setup>
import { computed } from 'vue';
import { DateTime } from 'luxon';
import { faEllipsis, faUserGroup, faPencil, faCopy, faTrashCan, faLock, faLocationPinLock, faPowerOff, faMask, faCalendarDays, faGrip } from '@fortawesome/free-solid-svg-icons';
import ButtonLink from '@/Components/ButtonLink.vue';
import DonutText from '@/Components/Charts/DonutText.vue';
import { hideAllPoppers } from 'floating-vue';

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
        class="relative"
        :class="{
                'border-secondary-600': pack.type == 'class_lesson',
                'border-blue-600': pack.type == 'service',
                'border-secondary-100': pack.type == 'hybrid',
                'border-primary-400': pack.type == 'location_pass',
                'border-cyan-500': pack.type == 'corporate',
            }">
        <div v-if="pack.type == 'hybrid'" class="absolute bg-gradient-to-r from-secondary-600 h-2 inset-x-0 top-[-8px] rounded-tl-md rounded-tr-md to-blue-600 w-full"></div>
        <div class="flex justify-between">
            <div class="flex-grow cursor-move">
                <font-awesome-icon class="px-2 py-1 text-gray-400" :icon="faGrip" />
            </div>
            <VDropdown placement="bottom-end" :distance="0" class="">
                <ButtonLink>
                    <font-awesome-icon :icon="faEllipsis" class="w-6 h-6 mx-2" :class="{'text-white': !pack.is_active, 'z-10': !pack.is_active}" />
                </ButtonLink>
                <template #popper>
                    <div class="p-2 w-40 space-y-4">
                        <ButtonLink 
                            :href="route('partner.packs.edit', pack)"
                            size="small"
                            class="flex justify-between w-full"
                            styling="default"
                            >
                            <font-awesome-icon :icon="faPencil" />
                            Edit
                        </ButtonLink>
                        <ButtonLink 
                            @click="$emit('copy', pack.id); hideAllPoppers();"
                            size="small"
                            class="flex justify-between w-full"
                            styling="secondary"
                            >
                            <font-awesome-icon :icon="faCopy" />
                            Duplicate
                        </ButtonLink>
                        <ButtonLink 
                            @click="$emit('toggle', pack.id); hideAllPoppers();"
                            size="small"
                            class="flex justify-between w-full"
                            :styling="pack.is_active ? 'default' : 'primary'"
                            >
                            <font-awesome-icon :icon="faPowerOff" />
                            {{pack.is_active ? 'Deactive' : 'Make active'}} 
                        </ButtonLink>
                        <ButtonLink 
                            @click="$emit('delete', pack.id); hideAllPoppers();"
                            size="small"
                            class="flex justify-between w-full"
                            styling="danger"
                            >
                            <font-awesome-icon :icon="faTrashCan" />
                            Delete
                        </ButtonLink>
                    </div>
                </template>
            </VDropdown>
        </div>
        <div class="flex items-center">
            <DonutText 
                    class="w-24 flex-shrink-0 ml-1"
                    :percent="donutPercent"
                    :title="donutPercent+'%'"
                    :subtitle="'active'"
                >
            </DonutText>
            <div class="px-2 text-gray-500">
                <div class="font-bold text-xl text-black leading-tight mb-2">{{pack.title}}</div>
                <VTooltip :triggers="['hover', 'click']" class="mb-2 border-b">
                    <div>
                        <font-awesome-icon :icon="faUserGroup" class="mr-2" />
                        {{pack.active_memberships_count}} active

                    </div>
                    <template #popper>
                        Pack total: {{pack.memberships_count}}
                    </template>
                </VTooltip>
                <div class="flex gap-8 items-center">
                    <font-awesome-icon
                        v-if="pack.is_restricted"
                        :icon="faLock"
                        v-tooltip="'Pack has restrictions'" />
                    <VTooltip v-if="pack.is_private" :triggers="['hover', 'click']">
                        <font-awesome-icon :icon="faMask" />
                        <template #popper>
                            Pack is private
                        </template>
                    </VTooltip>
                    <VTooltip v-if="pack.not_yet_active === true" :triggers="['hover', 'click']" >
                        <font-awesome-icon :icon="faCalendarDays" class="text-red-500" />
                        <template #popper>
                            <div class="w-72">
                                Pack will be available from 
                                {{DateTime.fromISO(pack.active_from)
                                    .setZone(business_settings.timezone)
                                    .toFormat(business_settings.date_format.format_js)}}
                            </div>
                        </template>
                    </VTooltip>
                    <VTooltip v-if="pack.will_become_inactive === true" :triggers="['hover', 'click']" >
                        <font-awesome-icon :icon="faCalendarDays" class="text-secondary-500" />
                        <template #popper>
                            <div class="w-72">
                                Pack will be available until the end of
                                {{DateTime.fromISO(pack.active_to)
                                    .setZone(business_settings.timezone)
                                    .toFormat(business_settings.date_format.format_js)}}
                            </div>
                        </template>
                    </VTooltip>
                    <VTooltip v-if="pack.became_inactive === true" :triggers="['hover', 'click']" >
                        <font-awesome-icon :icon="faCalendarDays" class="text-red-900" />
                        <template #popper>
                            <div class="w-72">
                                Pack was available until the end of
                                {{DateTime.fromISO(pack.active_to)
                                    .setZone(business_settings.timezone)
                                    .toFormat(business_settings.date_format.format_js)}}
                            </div>
                        </template>
                    </VTooltip>
                </div>
            </div>
        </div>

        <div v-if="pack.sub_title || pack.description" class="p-2">
            <div class="truncate">{{pack.sub_title}}</div>
            <VTooltip :triggers="['hover', 'click']">
                <div class="truncate">{{pack.description}}</div>

                <template #popper>
                    <div class="w-72">{{pack.description}}</div>
                </template>
            </VTooltip>
        </div>

        <div class="bg-gray-50 border border-gray-50 font-bold px-2 rounded-tr-lg text-sm mt-2 inline-block">Pricing options</div>

        <div class="bg-gray-50 px-2 py-4 space-y-2 flex-grow">
            <div v-if="pack.pack_prices.length" v-for="pack_price in pack.pack_prices" :key="pack_price.id" class="capitalize flex justify-between border-b last:border-none">
                <div>
                    <template v-if="pack_price.type == 'one_time'">
                    {{price_types.find(({value}) => value === pack_price.type)?.label}}
                    </template>
                    <template v-if="pack_price.type == 'recurring'">
                    {{pack_price.interval_adjective}}
                    </template>

                    <span v-if="pack_price.sessions > 0" class="font-bold mx-2">X{{pack_price.sessions}}</span>

                    <template v-if="pack_price.locations.length">
                    <VTooltip :triggers="['hover', 'click']" class="inline-block">
                        <div>
                            <font-awesome-icon :icon="faLocationPinLock" />
                        </div>
                        <template #popper>
                            <div class="flex flex-wrap gap-1 w-72">
                                <div class="w-full">Location restricted option</div>
                                <span v-for="restricted_location in pack_price.locations" :key="restricted_location.id" class="bg-red-100 font-semibold px-2 py-1 rounded-md text-red-800 text-xs">
                                    {{restricted_location.title}}
                                </span>
                            </div>
                        </template>
                    </VTooltip>
                    </template>
                </div>
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
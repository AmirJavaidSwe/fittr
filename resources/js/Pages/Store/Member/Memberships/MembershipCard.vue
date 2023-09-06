<script setup>
import { computed } from 'vue';
import { DateTime } from 'luxon';
import { faCalendarCheck, faCalendarDays, faHourglass } from '@fortawesome/free-regular-svg-icons';
import { faLocationPinLock, faEllipsis } from '@fortawesome/free-solid-svg-icons';
import ButtonLink from '@/Components/ButtonLink.vue';
import DonutText from '@/Components/Charts/DonutText.vue';
import CardIcon from '@/Components/CardIcon.vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    membership: {
        type: Object,
        required: true,
    },
    pack_types: {
        type: Array,
        required: true,
    },
    classtypes: {
        type: Array,
        required: true,
    },
    servicetypes: {
        type: Array,
        required: true,
    },
    locations: {
        type: Array,
        required: true,
    },
    business_settings: Object,
});

const typeLabel = computed(() => {
    return props.pack_types.find(el => el.value == props.membership.type);
});
const restrictedClasses = computed(() => {
    if(!props.classtypes || !props.membership.restrictions?.classtypes) return;
    return props.classtypes
        .filter(item => props.membership.restrictions.classtypes.some(e => e == item.id))
        .map(x => x.title)
        .join(', ');
});
const restrictedServices = computed(() => {
    if(!props.servicetypes || !props.membership.restrictions?.servicetypes) return;
    return props.servicetypes
        .filter(item => props.membership.restrictions?.servicetypes.some(e => e == item.id))
        .map(x => x.title)
        .join(', ');
});
const restrictedLocations = computed(() => {
    if(!props.locations || !props.membership.restrictions?.locations) return;
    return props.locations
        .filter(item => props.membership.restrictions?.locations.some(e => e == item.id))
        .map(x => x.title)
        .join(', ');
});

const donutPercent = computed(() => {
    if(props.membership.sessions === 0) return 100; //unlim
    const p =  (props.membership.sessions_active_credits_count / props.membership.sessions) * 100;
    return Math.round(p);
});
const donutTitle = computed(() => {
    if(props.membership.is_unlimited) return '∞';
    return donutPercent.value + '%';
});
const donutSubTitle = computed(() => {
    if(props.membership.is_unlimited) return '';
    return props.membership.sessions_active_credits_count + '/' + props.membership.sessions;
});

defineEmits(['cancel']);

</script>

<template>
    <div 
        class="p-2 relative"
        :class="{
                'border-secondary-600': membership.type == 'class_lesson',
                'border-blue-600': membership.type == 'service',
                'border-secondary-100': membership.type == 'hybrid',
                'border-primary-400': membership.type == 'default',
                'border-lime-600': membership.type == 'corporate',
            }">
        <div v-if="membership.type == 'hybrid'" class="absolute bg-gradient-to-r from-secondary-600 h-2 inset-x-0 top-[-8px] rounded-tl-md rounded-tr-md to-blue-600 w-full"></div>
        <div class="-m-2 bg-gray-700 mb-2 p-2 text-white flex flex-wrap justify-between">
            <div class="md:flex-1">
                <div class="font-bold">{{typeLabel.label}}</div>
                <div class="text-sm">{{typeLabel.description}}</div>
            </div>

            <!-- PRICE -->
            <div class="flex-shrink-0">
                <div class="flex font-bold">
                    <span>{{membership.currency_symbol}}</span>
                    <span class="text-3xl">{{membership.price_floor_formatted}}</span>
                    <span v-if="membership.price_decimals > 0">.{{membership.price_decimals}}</span>
                </div>
                <div v-if="membership.interval_count">{{membership.interval_human}}</div>
            </div>
        </div>

        <div class="flex flex-wrap gap-6 items-center justify-between">
            <div class="flex-grow font-bold" v-tooltip.left="'membership.title'">{{membership.title}}</div>
            <template v-if="membership.billing_type == 'recurring'">
                <div class="bg-primary-500 text-white text-sm rounded p-1">Subscription</div>
                <Dropdown align="right" width="48" :content-classes="['bg-gray-100', 'p-1', 'space-y-4']">
                    <template #trigger>
                        <ButtonLink>
                            <font-awesome-icon :icon="faEllipsis" class="w-10 h-10" />
                        </ButtonLink>
                    </template>
                    <template #content>
                        <ButtonLink 
                            @click="$emit('cancel', membership)"
                            size="small"
                            class="w-full"
                            styling="danger"
                            type="button"
                            >
                            Cancel
                        </ButtonLink>
                    </template>
                </Dropdown>
            </template>
        </div>
        <div v-if="membership.sub_title" class="text-grey" v-tooltip.left="'membership.sub_title'">{{membership.sub_title}}</div>
        <div v-if="membership.description" class="border-b-2 text-grey" v-tooltip.left="'membership.description'">{{membership.description}}</div>

        <div class="flex flex-wrap gap-4 items-center">
            <!-- area with donut charts, showing remaining credits for each of type class/service. Unlimited credits, show chart with infinity in the middle -->
            <template v-if="membership.sessions || membership.is_unlimited">
                <DonutText 
                    class="w-20"
                    :percent="donutPercent"
                    :title="donutTitle"
                    :subtitle="donutSubTitle"
                >
                </DonutText>

                <CardIcon>
                    <template #icon>
                        <font-awesome-icon :icon="faHourglass" class="h-8 w-8"  />
                    </template>
                    <template #title>
                        <template v-if="membership.is_unlimited">
                            <span class="font-bold text-2xl">Unlimited</span>
                        </template>
                        <template v-else>
                            <span class="font-bold text-3xl">{{membership.sessions_active_credits_count}}</span>
                            <span class="font-bold text-xl">/{{membership.sessions}}</span>
                        </template>
                    </template>
                    <template #default>
                        credits left
                    </template>
                </CardIcon>
                <!-- Fair Use Policy -->
                <CardIcon v-if="membership.is_unlimited && membership.is_fap">
                    <template #icon>
                        <font-awesome-icon :icon="faLocationPinLock" class="h-8 w-8"  />
                    </template>
                    <template #title>
                            <span class="font-bold text-2xl">Fair Use Policy</span>
                    </template>
                    <template #default>
                        <!-- <div class="border-b border-dashed text-grey"> -->
                            <VDropdown :popperTriggers="['hover']">
                                <button class="border-b border-dashed">Learn more</button>
                                <template #popper>
                                    <div class="p-2 w-80">
                                        Unlimited option is a subject for [LINK].<br>
                                        You can book up to {{membership.fap_value}} classes running on same day.
                                    </div>
                                </template>
                            </VDropdown>
                            <!-- </div> -->
                    </template>
                </CardIcon>
            </template>
            <!-- created_at -->
            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faCalendarDays" class="h-8 w-8" />
                </template>
                <template #title>
                    <span class="font-bold text-2xl">Started</span>
                </template>
                <template #default>
                        {{
                        DateTime.fromISO(membership.created_at).setZone(business_settings.timezone)
                                .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format.format_js)
                    }}
                </template>
            </CardIcon>
            <!-- expiration_date -->
            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faCalendarCheck" class="h-8 w-8" />
                </template>
                <template #title>
                    <span class="font-bold text-2xl">Expiry</span>
                </template>
                <template #default>
                    <div v-if="membership.is_expiring">
                        {{ DateTime.fromISO(membership.expiration_date).setZone(business_settings.timezone)
                                    .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format.format_js)
                        }} ({{membership.expiration}} {{membership.expiration_period}})
                    </div>
                    <div v-else>
                        Always active
                    </div>
                </template>
            </CardIcon>
        </div>

        <div v-if="membership.is_restricted" class="mt-2">
            <div class="font-bold text-red-500">Restrictions:</div>
            <div 
                v-if="membership.restrictions?.offpeak" 
                v-tooltip.right="'Class or service credit can be used for activities running during off-peak hours'"
                class="border-b border-dashed inline">Off-Peak Only</div>
            <div v-if="restrictedClasses">
                Valid for {{restrictedClasses}} <b>classes</b> only.
            </div>
            <div v-if="restrictedServices">
                Valid for {{restrictedServices}} <b>services</b> only.
            </div>
            <div v-if="restrictedLocations">
                Valid for {{restrictedLocations}} <b>location(s)</b> only.
            </div>
        </div>
    </div>
</template>
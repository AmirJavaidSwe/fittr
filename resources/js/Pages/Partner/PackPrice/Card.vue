<script setup>
import { computed, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { faGear, faLocationPinLock, faRecycle, faUserPlus, faHourglassEnd, faShopLock } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    price: {
        type: Object,
        required: true,
    },
    pack_type: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});
const restricted_locations = ref([]);
const locationRestrictionsTooltip = computed(() => {
    let ids = props.price.location_ids;
    let count = ids.length;
    restricted_locations.value = props.price.locations.filter(o => ids.includes(o.id));
    return 'This option restricted to ' + count + ' location' + (count > 1 ? 's':'');
});
const isRecurring = computed(() => {
    return props.price.type == 'recurring';
});
const showRenewable = computed(() => {
    return props.price.is_renewable;
});
const showIntro = computed(() => {
    return props.price.is_intro;
});
const showExpiration = computed(() => {
    return props.price.is_expiring;
});
const isUnlimited = computed(() => {
    return props.price.is_unlimited && props.price.type == 'recurring';
});
const isPassType = computed(() => {
    return props.pack_type == 'location_pass';
});

defineEmits(['toggle', 'edit', 'delete']);

</script>
<template>
    <div class="flex flex-col shadow-md w-80">
        <div 
        class="flex font-medium items-center justify-between space-between"
        :class="{
            'bg-opacity-10': !price.is_active,
            'bg-primary-300': price.type == 'one_time',
            'bg-secondary-500': price.type == 'recurring',
            }"
        >
            <div class="p-2 font-bold">{{label}}</div>
            <Dropdown align="right" width="48" :content-classes="['bg-gray-100', 'p-1', 'space-y-4']">
                <template #trigger>
                <font-awesome-icon :icon="faGear" class="cursor-pointer p-2" />
                </template>
                <template #content>
                    <ButtonLink 
                        @click="$emit('toggle')"
                        :styling="price.is_active ? 'default' : 'primary'"
                        size="small"
                        class="w-full"
                        type="button"
                        >
                        {{price.is_active ? 'Deactive' : 'Make active'}} 
                    </ButtonLink>
                    <ButtonLink 
                        @click="$emit('edit')"
                        styling="secondary"
                        size="small"
                        class="w-full"
                        type="button"
                        >
                        Edit
                    </ButtonLink>
                    <ButtonLink 
                        @click="$emit('delete')"
                        styling="danger"
                        size="small"
                        class="w-full"
                        type="button"
                        >
                        Delete 
                    </ButtonLink>
                </template>
            </Dropdown>
        </div>
        <div class="flex-grow p-2 space-y-2" :class="{'bg-gray-100': !price.is_active}">
            <div> 
                <span class="font-bold" :title="price.currency.toUpperCase()">{{price.price_formatted}}</span>
                <span v-if="price.interval_count">&nbsp;{{price.interval_human}}</span>
            </div>

            <div v-if="isUnlimited"> 
                <span class="font-bold text-2xl">Unlimited {{price.taxonomy_sessions}}</span>
            </div>

            <template v-if="isPassType">
                <div v-if="price.sessions > 0"> 
                    <span class="font-bold text-2xl" :title="price.sessions">{{price.sessions}}</span>
                    <span class="ml-1">{{price.taxonomy_sessions}}&nbsp;{{price.interval_adjective}}</span>
                </div>
                <div v-else>
                    Unlimited location visits while active
                </div>
            </template>
            <template v-else>
                <div> 
                    <span class="font-bold text-2xl" :title="price.sessions">{{price.sessions}}</span>
                    <span class="ml-1">{{price.taxonomy_sessions}}</span>
                </div>
            </template>

            <div v-if="price.location_ids.length" class="flex flex-wrap items-center gap-2" v-tooltip="locationRestrictionsTooltip">
                <font-awesome-icon :icon="faLocationPinLock" />
                <span v-for="location in restricted_locations" :key="location.id" class="bg-red-100 font-semibold px-2 py-2 rounded-md text-red-800 text-xs">
                    {{location.title}}
                </span>
            </div>
        </div>
        <div class="bg-gray-100 flex flex-wrap gap-2">
            <font-awesome-icon
                v-if="showRenewable"
                :icon="faRecycle"
                class="w-4 h-4 m-2 p-1 bg-white rounded"
                v-tooltip="'Auto renewable option'" 
                />
            <font-awesome-icon
                v-if="showIntro"
                :icon="faUserPlus"
                class="w-4 h-4 m-2 p-1 bg-white rounded"
                v-tooltip="'Intro option. Available to new or existing members who have never made a purchase in the past'" 
                />
            <font-awesome-icon 
                v-if="showExpiration"
                :icon="faHourglassEnd"
                class="w-4 h-4 m-2 p-1 bg-white rounded"
                v-tooltip="(isPassType ? 'Membership' : 'Session credits') + ' will expire. Expiration period: '+price.expiration+' '+price.expiration_period" 
                />
            <font-awesome-icon 
                v-if="isRecurring && price.min_term > 0"
                :icon="faShopLock"
                class="w-4 h-4 m-2 p-1 bg-white rounded"
                v-tooltip="'This option has min term enabled for subscription.'" 
                />
        </div>
    </div>
</template>
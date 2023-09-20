<script setup>
import { computed } from 'vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import PriceOption from './PriceOption.vue';

const props = defineProps({
    pack: {
        type: Object,
        required: true,
    },
    state_buttons: {
        type: Object,
        required: true,
    },
    location: {
        type: String,
        required: true,
    },
    locations: {
        type: Array,
        required: true,
    },
    isLocked: {
        type: Boolean,
        required: false,
    },
    classtypes: Array,
    servicetypes: Array,
});

const restrictedClasses = computed(() => {
    if(!props.classtypes || !props.pack.restrictions?.classtypes) return;
    return props.classtypes
        .filter(item => props.pack.restrictions.classtypes.some(e => e == item.id))
        .map(x => x.title)
        .join(', ');
});
const restrictedServices = computed(() => {
    if(!props.servicetypes || !props.pack.restrictions?.servicetypes) return;
    return props.servicetypes
        .filter(item => props.pack.restrictions?.servicetypes.some(e => e == item.id))
        .map(x => x.title)
        .join(', ');
});
const show = function (price){
    // show location unrestricted price || matching selected ID
    if(price.locations.length === 0 || price.locations.find(el => el.id == props.location)){
        return true;
    }

    //all locations option
    //Only active locations should match price's active locations
    return props.locations.length == price.locations.filter(o => o.status == true).length;
};

defineEmits(['priceSelected', 'buy']);
</script>

<template>
    <div 
        class="relative"
        :class="{
                'border-secondary-600': pack.type == 'class_lesson',
                'border-blue-600': pack.type == 'service',
                'border-secondary-100': pack.type == 'hybrid',
                'border-primary-400': pack.type == 'location_pass',
                'border-lime-600': pack.type == 'corporate',
            }">
        <div v-if="pack.type == 'hybrid'" class="absolute bg-gradient-to-r from-secondary-600 h-2 inset-x-0 top-[-8px] rounded-tl-md rounded-tr-md to-blue-600 w-full"></div>
        <div class="font-bold">{{pack.title}}</div>
        <div v-if="pack.sub_title" class="text-grey">{{pack.sub_title}}</div>
        <div v-if="pack.pack_prices.length" class="py-2">
            <PriceOption 
                v-for="price in pack.pack_prices"
                :key="price.id"
                :pack="pack"
                :price="price"
                :state_buttons="state_buttons"
                class="flex justify-between items-center mb-4 last:mb-0 border-b-2"
                :class="{'text-primary-400': state_buttons[pack.id].selected_price_id == price.id}"
                v-show="show(price)"
                @priceSelected="(pack_id, price_id) => $emit('priceSelected', pack_id, price_id)"
                >
            </PriceOption>
        </div>
        <div v-if="pack.description" class="border-b-2 text-grey">{{pack.description}}</div>
        <div v-if="pack.is_restricted" class="mt-2 text-grey">
            <div class="font-bold">Restrictions:</div>
            <div v-if="pack.restrictions?.offpeak" v-tooltip="'Class or service credit can be used for activities running during off-peak hours'" class="border-b border-dashed text-grey">Off-Peak Only</div>
            <div v-if="restrictedClasses">
                Valid for {{restrictedClasses}} <b>classes</b> only.
            </div>
            <div v-if="restrictedServices">
                Valid for {{restrictedServices}} <b>services</b> only.
            </div>
        </div>
        <div class="flex flex-grow items-end">
            <ButtonLink 
                type="button"
                styling="secondary"
                size="small"
                class="w-full justify-center mt-4"
                @click="$emit('buy')"
                :disabled="isLocked || !state_buttons[pack.id]?.enabled"
                v-text="state_buttons[pack.id]?.button_text"
                v-if="state_buttons[pack.id]"
                >
            </ButtonLink>
        </div>
    </div>
</template>
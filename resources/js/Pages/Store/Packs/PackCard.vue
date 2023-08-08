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
    isLocked: {
        type: Boolean,
        required: false,
    },
    classtypes: Array,
});

const restrictedClasses = computed(() => {
    return props.classtypes
        .filter(item => props.pack.restrictions.classtypes.some(e => e == item.id))
        .map(x => x.title)
        .join(', ');
});

defineEmits(['priceSelected', 'buy']);
</script>

<template>
    <div :class="{
                'border-secondary-600': pack.type == 'class_lesson',
                'border-lime-600': pack.type == 'corporate',
            }">
        <div class="font-bold">{{pack.title}}</div>
        <div v-if="pack.sub_title" class="text-grey">{{pack.sub_title}}</div>
        <div v-if="pack.prices.length" class="py-2">
            <PriceOption 
                v-for="price in pack.prices"
                :key="price.id"
                :pack="pack"
                :price="price"
                :state_buttons="state_buttons"
                :location="location"
                class="flex justify-between items-center mb-4 last:mb-0 border-b-2"
                :class="{'text-primary-400': state_buttons[pack.id].selected_price_id == price.id}"
                v-show="price.locations.length === 0 || price.locations.find(el => el.id == location)"
                @priceSelected="(pack_id, price_id) => $emit('priceSelected', pack_id, price_id)"
                >
            </PriceOption>
        </div>
        <div v-if="pack.description">{{pack.description}}</div>
        <div v-if="pack.is_restricted" class="mt-2 border-t-2 text-grey">
            <div class="font-bold">Restrictions:</div>
            <div v-if="pack.restrictions?.offpeak" v-tooltip="'Class or service credit can be used for activities running during off-peak hours'" class="border-b border-dashed text-grey">Off-Peak Only</div>
            <div v-if="pack.restrictions?.classtypes">
                Valid for {{restrictedClasses}} classes only.
            </div>
            <!-- <div v-if="pack.restrictions?.servicetypes">
                Valid for .... services only.
            </div> -->
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
<script setup>
import { faCircle, faCircleCheck } from '@fortawesome/free-regular-svg-icons';

const props = defineProps({
    price: {
        type: Object,
        required: false,
    },
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
});

defineEmits(['priceSelected']);

</script>
<template>
    <div 
        class="flex justify-between items-center mb-4 last:mb-0 border-b-2"
        :class="{'text-primary-400': state_buttons[pack.id] ? state_buttons[pack.id].selected_price_id == price.id : false}"
        v-show="price.locations.length === 0 || price.locations.find(el => el.id == location)"
        >
        <div class="flex gap-2 pb-1">
            <div class="flex font-bold">
                <span>{{price.currency_symbol}}</span>
                <span class="text-3xl">{{price.price_floor_formatted}}</span>
                <span v-if="price.price_decimals > 0">.{{price.price_decimals}}</span>
            </div>
            <span class="border"></span>
            <span class="font-bold text-3xl">{{price.sessions}}</span>
            <div class="leading-4">
                <div class="self-end text-grey">classes</div>
                <div v-if="price.is_renewable" class="border-b border-dashed dashed text-grey">
                    <VDropdown :popperTriggers="['hover']">
                        <button>auto top-up</button>
                        <template #popper>
                            <p class="p-2 w-80">When your session credit balance reaches zero, we will automatically top up your balance by purchasing this option for you.</p>
                        </template>
                    </VDropdown>
                </div>
            </div>
        </div>
        <div v-if="pack.prices.length > 1" class="cursor-pointer p-2" @click="$emit('priceSelected', pack.id, price.id)">
            <font-awesome-icon v-if="state_buttons[pack.id].selected_price_id == price.id" :icon="faCircleCheck" />
            <font-awesome-icon v-else :icon="faCircle" />
        </div>
    </div>
</template>
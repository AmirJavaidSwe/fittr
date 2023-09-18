<script setup>
import { computed } from 'vue';
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
    }
});

const isUnlimited = computed(() => {
  return props.price.is_unlimited && props.price.type == 'recurring';
});

const isPassType = computed(() => {
    return props.pack.type == 'location_pass';
});

defineEmits(['priceSelected']);

</script>
<template>
    <div 
        class="flex flex-wrap justify-between items-center mb-4 last:mb-0 border-b-2"
        :class="{'text-primary-400': state_buttons[pack.id] ? state_buttons[pack.id].selected_price_id == price.id : false}"
        >
        <div class="flex flex-wrap gap-2 pb-1">
            <div>
                <div class="flex font-bold">
                    <span>{{price.currency_symbol}}</span>
                    <span class="text-3xl">{{price.price_floor_formatted}}</span>
                    <span v-if="price.price_decimals > 0">.{{price.price_decimals}}</span>
                </div>
                <div v-if="price.interval_count">{{price.interval_adjective}}</div>
            </div>
            <span class="border"></span>

            <!-- pack.type == 'location_pass' -->
            <template v-if="isPassType">
                <!-- Show number of passes -->
                <template v-if="price.sessions > 0">
                    <span class="font-bold text-3xl">{{price.sessions}}</span>
                    <div class="leading-4">
                        <div class="text-grey">{{price.taxonomy_sessions}}</div>
                        <div v-if="price.is_renewable" class="border-b border-dashed text-grey">
                            <VDropdown :popperTriggers="['hover']">
                                <button>auto top-up</button>
                                <template #popper>
                                    <p class="p-2 w-80">When your {{price.taxonomy_sessions}} balance reaches zero, we will automatically top up your balance by purchasing this option for you.</p>
                                </template>
                            </VDropdown>
                        </div>
                    </div>
                </template>

                <!-- Show pass expiration period -->
                <template v-else-if="price.is_expiring">
                    <span class="font-bold">{{price.expiration}} {{price.expiration_period}} pass</span>
                </template>
            </template>

            <!-- pack.type != 'location_pass' -->
            <template v-else>
            <span class="font-bold text-3xl" v-if="isUnlimited">&infin;</span>
            <span class="font-bold text-3xl" v-else>{{price.sessions}}</span>
            <div class="leading-4">
                <div class="text-grey">{{price.taxonomy_sessions}}</div>
                <div v-if="price.is_renewable" class="border-b border-dashed text-grey">
                    <VDropdown :popperTriggers="['hover']">
                        <button>auto top-up</button>
                        <template #popper>
                            <p class="p-2 w-80">When your {{price.taxonomy_sessions}} balance reaches zero, we will automatically top up your balance by purchasing this option for you.</p>
                        </template>
                    </VDropdown>
                </div>
                <div v-if="isUnlimited" class="border-b border-dashed text-grey">
                    <VDropdown v-if="price.is_fap" :popperTriggers="['hover']">
                        <button>Fair Use Policy</button>
                        <template #popper>
                            <div class="p-2 w-80">
                                Unlimited option is a subject for [Fair Use Policy].<br>
                                Maximum number of classes running same day you can book is: {{price.fap_value}}
                            </div>
                        </template>
                    </VDropdown>
                </div>
            </div>
            </template>
        </div>
        <div v-if="pack.pack_prices.length > 1" class="cursor-pointer p-2" @click="$emit('priceSelected', pack.id, price.id)">
            <font-awesome-icon v-if="state_buttons[pack.id].selected_price_id == price.id" :icon="faCircleCheck" />
            <font-awesome-icon v-else :icon="faCircle" />
        </div>
    </div>
</template>
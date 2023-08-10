<script setup>
import { ref, reactive, computed } from 'vue';
import { router, useForm, usePage } from "@inertiajs/vue3";
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PackCard from './PackCard.vue';

const props = defineProps({
    pack: {
        type: Object,
        required: true,
    },
    price_buttons: {
        type: Object,
        required: true,
    },
    price_buttons_text: {
        type: Object,
        required: true,
    },
    locations: {
        type: Array,
        required: true,
    },
    classtypes: {
        type: Array,
        required: true,
    },
});
const subdomain = ref(usePage().props.business_settings.subdomain);
const isLocked = ref(false);
const buy = (pack_id) => {
    isLocked.value = true;
    router.post( route('ss.payments.index', {subdomain: subdomain.value, price: state_buttons[pack_id].selected_price_id}), {}, {
        preserveScroll: true,
        // onBefore: () => confirm('Are you sure?'),
        onFinish: (visit) => {
            setTimeout(() => {
                isLocked.value = false;
            }, 3000);
        },
    });
};
const location = ref('');
const locationTitle = computed(() => {
  return location.value == '' ? 'All locations' : props.locations.find(el => el.id == location.value).title;
});
const filterByLocation = () => {
    priceSelected(null);//reset buttons state
};
const state_buttons_default = props.price_buttons;
const state_buttons = reactive(props.price_buttons);
const priceSelected = (pack_id, price_id) => {
    if(pack_id === null){
        for (const property in state_buttons) {
            //packs with single price AND (unrestricted OR restricted location will be ON if location match)
            if( state_buttons[property][0].length == 1 ) {
                let single_price_pass = state_buttons[property][0][0].locations.length == 0 || state_buttons[property][0][0].locations.includes(parseInt(location.value));

                state_buttons[property].selected_price_id = single_price_pass ? state_buttons[property][0][0].id : null;
                state_buttons[property].enabled = single_price_pass;
                state_buttons[property].button_text = single_price_pass ? state_buttons[property][0][0].text : props.price_buttons_text.na;
            }
            //packs with multiple prices should be unselected
            if( state_buttons[property][0].length > 1 ) {
                state_buttons[property].selected_price_id = null;
                state_buttons[property].enabled = false;
                state_buttons[property].button_text = props.price_buttons_text.select;
            }
        }
        return;
    }

    state_buttons[pack_id].selected_price_id = price_id;
    state_buttons[pack_id].enabled = true;
    state_buttons[pack_id].button_text = state_buttons[pack_id][0].find(el => el.id == price_id).text;
};
</script>

<template>
    <Section bg="bg-transparent" class="mx-auto max-w-7xl">
        <div class="text-center mb-4 sticky top-0 bg-gray-300">
            Private Membership for
            <div class="inline-block">
                <InputLabel for="location" value="Location" class="sr-only" />
                <SelectInput
                    id="location"
                    v-model="location"
                    @change="filterByLocation"
                    :options="[{id:'',title:'All locations'}, ...props.locations]"
                    option_value="id"
                    option_text="title"
                    class="bg-transparent border-0 focus:border-0 font-bold"
                >
                </SelectInput>
            </div>
        </div>
        <PackCard 
            :key="pack.id"
            :pack="pack"
            :state_buttons="state_buttons"
            :location="location"
            :isLocked="isLocked"
            :classtypes="classtypes"
            class="bg-white rounded-md border-t-8 p-2 w-80 flex flex-col"
            @priceSelected="(pack_id, price_id) => priceSelected(pack_id, price_id)"
            @buy="buy(pack.id)"
            >
        </PackCard>
    </Section>
</template>

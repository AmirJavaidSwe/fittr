<script setup>
import { ref, reactive, computed } from 'vue';
import { router, useForm, usePage } from "@inertiajs/vue3";
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PackCard from './PackCard.vue';

const props = defineProps({
    packs: {
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
});
const subdomain = ref(usePage().props.business_seetings.subdomain);
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
        <div class="text-xl font-bold text-center">
            Pricing
        </div>
        <div class="text-center mb-4 sticky top-0 bg-gray-300">
            Purchase Class Pack or Membership for
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

        <!-- CLASSES  -->
        <div class="text-xl font-bold">
            {{locationTitle}} Class Packs and Memberships
        </div>
        <div>Ultimate flexibility. Grab a pack for classes you love as one time purchase or subscribe. The more you buy, the lower the price per class.</div>
        <div class="flex flex-wrap gap-4 mt-4 mb-16">
            <PackCard 
                v-for="pack in packs.filter(el => el.type == 'class_lesson')"
                :key="pack.id"
                :pack="pack"
                :state_buttons="state_buttons"
                :location="location"
                :isLocked="isLocked"
                class="bg-white rounded-md border-t-8 p-2 w-80 flex flex-col"
                @priceSelected="(pack_id, price_id) => priceSelected(pack_id, price_id)"
                @buy="buy(pack.id)"
                >
            </PackCard>
        </div>

        <!-- CORPORATE -->
        <div class="text-xl font-bold">
            {{locationTitle}} Corporate Memberships
        </div>
        <div>Support your teams fitness with a corporate membership to be shared amongst your colleagues. Classes are "use it or lose it". The pack will give you unique redemption code, your employees can use to redeem class credits.</div>

        <div class="flex flex-wrap gap-4 mt-4">
            <PackCard 
                v-for="pack in packs.filter(el => el.type == 'corporate')"
                :key="pack.id"
                :pack="pack"
                :state_buttons="state_buttons"
                :location="location"
                :isLocked="isLocked"
                class="bg-white rounded-md border-t-8 p-2 w-80 flex flex-col"
                @priceSelected="(pack_id, price_id) => priceSelected(pack_id, price_id)"
                @buy="buy(pack.id)"
                >
            </PackCard>
        </div>
    </Section>
</template>

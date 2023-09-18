<script setup>
import { reactive, computed, ref } from 'vue';
import {useForm} from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from '@/Components/TextArea.vue';
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import Switcher from "@/Components/Switcher.vue";
import Datepicker from '@vuepic/vue-datepicker';
import Multiselect from '@vueform/multiselect';
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import { RadioGroup, RadioGroupOption } from '@headlessui/vue';
import '@vuepic/vue-datepicker/dist/main.css';
import '@vueform/multiselect/themes/tailwind.css';

const props = defineProps({
    pack_types: Array,
    periods: Array,
    price_types: Array,
    locations: Array,
    default_currency: String,
    pack_type: String,
    price: Object,
    is_new_price: Boolean,
});

const emit = defineEmits(['created', 'updated']);

const formPrice = useForm(props.price);

const upsertPrice = () => {
    if(props.is_new_price){
            formPrice.transform((data) => ({
                ...data,
                pass_mode: pass_mode.value,
                min_term: hasMinTerm.value ? data.min_term : null,
                fixed_count: data.is_ongoing ? null : data.fixed_count,
            }))
            .post(route('partner.packs.price.store', { pack : formPrice.priceable_id }), {
                preserveScroll: true,
                onSuccess: () => emit('created'),
            });
    } else {
        //props we need
        // #1 location_pass (Membership Type), one_time
        //  is_renewable (is_renewable or is_intro but not both on)
        //  is_intro
        //  expiration (multiplier) 
        //  expiration_period (day, week, month, year) 

        // #1.1 location_pass (Membership Type), recurring
        //  is_ongoing
        //  fixed_count
        //  expiration (multiplier) 
        //  expiration_period (day, week, month, year) 

        // #2 [class_lesson, service, hybrid],  (Membership Type), one_time
        //  sessions (number of)
        //  is_expiring (credits, bool)
        //  expiration (multiplier) 
        //  expiration_period (day, week, month, year) 
        //  is_renewable (is_renewable or is_intro but not both on)
        //  is_intro

        // #2 [class_lesson, service, hybrid],  (Membership Type), recurring
        //  sessions (number of)
        //  is_unlimited (bool)
        //  is_fap (bool)
        //  fap_value (Number) 
        //  is_expiring (credits, bool)
        //  expiration (multiplier) 
        //  expiration_period (day, week, month, year) 
        //  is_ongoing
        //  fixed_count

        // #3 corporate (Membership Type), one_time
        //  sessions (number of)
        //  is_expiring (credits, bool)
        //  expiration (multiplier) 
        //  expiration_period (day, week, month, year) 


        formPrice
        .transform((data) => ({
            ...data,
            pack_type: props.pack_type,
            pass_mode: pass_mode.value,
            min_term: hasMinTerm.value ? data.min_term : null,
            fixed_count: data.is_ongoing ? null : data.fixed_count,
            action: 'edit',
        }))
        .put(route('partner.packs.price.update', { price: formPrice.id } ), {
            preserveScroll: true,
            onSuccess: () => emit('updated'),
        });
    }

};

const isPassType = computed(() => {
  return props.pack_type == 'location_pass';
});
const isCorporateType = computed(() => {
  return props.pack_type == 'corporate';
});

const pass_mode = ref(isPassType.value && formPrice.sessions > 0);

// is_renewable and is_intro can't be ON at the same time
const checkIntro = (el, v) => {
    if(el == 'is_intro' && v){
        formPrice.is_renewable = false;
    }
    if(el == 'is_renewable' && v){
        formPrice.is_intro = false;
    }
};

const showExpiration = computed(() => {
  return formPrice.is_expiring;
});
const isRecurring = computed(() => {
  return formPrice.type == 'recurring';
});
const showSessions = computed(() => {
    if(isPassType.value){
        return pass_mode.value === true;
    }
    if(isRecurring.value === false){
        return true;
    }
    if(isRecurring.value && formPrice.is_unlimited === false){
        return true;
    }
    return false;
});
const showUnlim = computed(() => {
    return isRecurring.value && !isPassType.value && !isCorporateType.value;
});
const showFap = computed(() => {
    return showUnlim.value && formPrice.is_unlimited;
});
const showFapValue = computed(() => {
    return showFap.value && formPrice.is_fap;
});
const showRenewable = computed(() => {
    return !isRecurring.value && (isPassType.value && pass_mode.value);
});
const showIntro = computed(() => {
    return !isRecurring.value && props.pack_type != 'corporate';
});
const expirationTitle = computed(() => {
    let title = 'Session credits';
    if(isPassType.value){
        title = pass_mode.value === true ? 'Passes' : 'Membership';
    }
    return title + ' expiration';
});
const expirationDescription = computed(() => {
    if(!isPassType.value){
        return 'Session credits ' + (formPrice.is_expiring ? 'will' : 'never') + ' expire (since day of creation)';
    }
    // isPassType.value === true case
    if(pass_mode.value === true){
        if(formPrice.is_expiring){
            return 'Unused passes will expire';
        }
        return isRecurring.value ? 
            'Unused passes from each billing cycle will remain active and carried over':
            'Unused passes will remain active indefinitely';
    } 
    if(pass_mode.value === false){
        if(formPrice.is_expiring){
            return isRecurring.value ? 
            'Membership will expire on the next day of selected period':
            'Membership will expire';
        }
        return isRecurring.value ? 
            'Membership will expire on the next day of paid period':
            'Membership will remain active indefinitely';
    }
});

const hasMinTerm = ref(isRecurring.value && formPrice.min_term > 0);

</script>

<template>
    <FormSection @submitted="upsertPrice">
        <template #title>Pack type: {{pack_types.find(element => element.value == pack_type).label}}</template>
        <template #description>{{!is_new_price ? price_types.find(element => element.value == formPrice.type).label+' membership' : ''}}</template>
        <template #form>
            <slot />
            <!-- Type -->
            <div v-if="is_new_price" class="flex">
                <RadioGroup v-model="formPrice.type" class="cursor-pointer space-y-4">
                    <RadioGroupOption 
                        as="template"
                        v-for="option in price_types"
                        :key="option.value"
                        :value="option.value"
                        v-slot="{ active, checked }"
                        >
                        <div>
                            <span class='block sm:inline-block w-28 mr-4 px-5 py-2 rounded-lg text-center transition' :class="checked ? 'font-bold text-white bg-green-700' : 'bg-gray-50'">{{ option.label }}</span>
                            <span class='transition' :class="checked ? '' : 'text-gray-400'">{{ option.description }}</span>
                        </div>
                    </RadioGroupOption>
                </RadioGroup>
            </div>
            <InputError :message="formPrice.errors.type" class="mt-2"/>

            <!-- Price -->
            <div v-if="is_new_price">
                <InputLabel for="price" value="Amount" />
                <div class="flex items-center gap-2">
                    <TextInput
                        id="price"
                        v-model="formPrice.price"
                        type="number"
                        min="0"
                        step="0.01"
                        class="mt-1"
                    />
                    <span>{{default_currency.toUpperCase()}}</span>
                </div>
                <InputError :message="formPrice.errors.price" class="mt-2" />
            </div>
            <div v-else>
                <span class="font-bold">{{formPrice.price_formatted_full}}</span>
                <span v-if="formPrice.interval_count">&nbsp;{{formPrice.interval_human}}</span>
            </div>

            <div v-if="isPassType">
                <Switcher
                    v-model="pass_mode"
                    title="Pass mode"
                    description="Should this membership provide unlimited passed while active or be based on fixed number of entrances"/>
            </div>

            <!-- Sessions -->
            <div v-if="showSessions">
                <InputLabel for="sessions" :value="isPassType ? 'Passes' : 'Session credits'" />
                <div class="text-gray-500 text-xs">
                    <template v-if="isCorporateType">
                    System will generate unique redemption code upon checkout. The code can be redeemed by anyone or be restricted by email domain. Everytime redemption occurs, single session credit will be deposited on members' account and can be then used to make a booking. Total number of code redemptions can't go over number below.
                    </template>
                    <template v-else-if="isPassType">
                    Membership will provide a number of one-time location passes. Pass is used after each time member checks into premises.
                    </template>
                    <template v-else>
                    Membership will provide a number of session credits. Credits can be used to make bookings for classes and/or services.
                    </template>
                </div>
                <TextInput
                    id="sessions"
                    v-model="formPrice.sessions"
                    type="number"
                    min="0"
                    step="1"
                    class="mt-1"
                />
                <InputError :message="formPrice.errors.sessions" class="mt-2" />
            </div>

            <!-- is_unlimited => WHEN recurring + !default + !corporate -->
            <div v-if="showUnlim">
                <Switcher
                    v-model="formPrice.is_unlimited"
                    title="Unlimited pack"
                    description="Members can make unlimited bookings when this option enabled"/>
                <InputError :message="formPrice.errors.is_unlimited" class="mt-2"/>
            </div>

            <!-- is_fap => showUnlim + is_unlimited -->
            <div v-if="showFap">
                <Switcher
                    v-model="formPrice.is_fap"
                    title="Fair access policy"
                    description="Enable FAP to prevent booking abuse on unlimited packs. With FAP you may, for example, limit member to 2 classes per day."/>
                <InputError :message="formPrice.errors.is_fap" class="mt-2"/>
            </div>

            <!-- FAP value -->
            <div v-if="showFapValue">
                <InputLabel for="fap_value" value="Max classes" />
                <div class="text-gray-500 text-xs">Member with unlimited subscription will not be able to go over this number and book more classes scheduled on same day. Minimum is 1.</div>
                <TextInput
                    id="price"
                    v-model="formPrice.fap_value"
                    type="number"
                    min="1"
                    step="1"
                    class="mt-1"
                />
                <InputError :message="formPrice.errors.fap_value" class="mt-2" />
            </div>

            <!-- is_expiring (session credits) -->
            <div>
                <Switcher
                    v-model="formPrice.is_expiring"
                    :title="expirationTitle"
                    :description="expirationDescription"/>
                <InputError :message="formPrice.errors.is_expiring" class="mt-2"/>
            </div>

            <div v-if="showExpiration">
                <InputLabel for="expiration" value="In"/>
                <div class="flex flex-wrap my-1 gap-2">
                    <!-- Expiry integer value -->
                    <TextInput
                        id="expiration"
                        v-model="formPrice.expiration"
                        type="number"
                        min="1"
                        class="w-20"
                        />
                        <!-- Expiry period selection -->
                        <RadioGroup v-model="formPrice.expiration_period" class="flex cursor-pointer rounded-lg shadow-md">
                            <RadioGroupOption 
                            as="template"
                            v-for="option in periods"
                            :key="option.value"
                            :value="option.value"
                            v-slot="{ active, checked }"
                            >
                                <span class='px-5 py-2 rounded-lg transition' :class="checked ? 'bg-green-400' : ''">{{ option.label }}</span>
                            </RadioGroupOption>
                        </RadioGroup>
                </div>
                <div v-if="isCorporateType" class="text-gray-500 text-xs">Once code has been redeemed for session credit, it will expire after period of time specified above.</div>
                <div v-else-if="isRecurring" class="text-gray-500 text-xs">
                    You can match {{isPassType ? 'membership' : 'session credits'}} to be inline with your billing cycle or make them expire before/after next billing cycle.
                </div>
                <div v-else-if="isPassType" class="text-gray-500 text-xs">Set the period for membership expiration.</div>
                <div v-else class="text-gray-500 text-xs">Session credits will expire on next day of selected period. The lifecycle of session credit starts on the checkout day.</div>
                <InputError :message="formPrice.errors.expiration" class="mt-2"/>
                <InputError :message="formPrice.errors.expiration_period" class="mt-2"/>
            </div>

            <!-- Recurring interval -->
            <div v-if="isRecurring && is_new_price">
                <InputLabel for="interval_count" value="Billing interval"/>
                <div class="flex flex-wrap my-1 gap-2">
                    <!-- interval integer value -->
                    <TextInput
                        id="interval_count"
                        v-model="formPrice.interval_count"
                        type="number"
                        min="1"
                        class="w-20"
                        />
                        <!-- interval calendar period string -->
                        <RadioGroup v-model="formPrice.interval" class="flex cursor-pointer rounded-lg shadow-md">
                            <RadioGroupOption 
                            as="template"
                            v-for="option in periods"
                            :key="option.value"
                            :value="option.value"
                            v-slot="{ active, checked }"
                            >
                                <span class='px-5 py-2 rounded-lg transition' :class="checked ? 'bg-green-400' : ''">{{ option.label }}</span>
                            </RadioGroupOption>
                        </RadioGroup>
                </div>
                <div class="text-gray-500 text-xs">You may create most flexible billing cycle by selecting the period and multiplier.</div>
                <div class="text-gray-500 text-xs">For example, Billing interval = 2 and period Week bills every 2 weeks. Maximum of one year interval allowed.</div>
                <InputError :message="formPrice.errors.interval_count" class="mt-2"/>
                <InputError :message="formPrice.errors.interval" class="mt-2"/>
            </div>

            <!-- min_term/hasMinTerm ref -->
            <div v-if="isRecurring">
                <Switcher
                    v-model="hasMinTerm"
                    title="Subscription min term"
                    :description="hasMinTerm ? 
                    'Member must complete min term, subscription charges will continue until min term is served' :
                    'Member can cancel anytime'
                    "/>
            </div>
            <!-- min_term -->
            <div v-if="hasMinTerm">
                <InputLabel for="min_term" value="Number of cycles"/>
                <div class="text-gray-500 text-xs">Subscription will charge a customer set number of times before it may be cancelled.</div>
                <TextInput
                        id="min_term"
                        v-model="formPrice.min_term"
                        type="number"
                        min="1"
                        class="w-20"
                        />
                <InputError :message="formPrice.errors.min_term" class="mt-2"/>
            </div>

            <!-- is_ongoing -->
            <div v-if="isRecurring">
                <Switcher
                    v-model="formPrice.is_ongoing"
                    title="Subscription duration"
                    :description="formPrice.is_ongoing ?
                    'Subscrption runs indefinitely, unless cancelled' :
                    'Subscrption should complete after specified number of billing cycles. If min term is set, this number must be equal or greater than min term value.'
                "/>
                <InputError :message="formPrice.errors.is_ongoing" class="mt-2"/>
            </div>

            <!-- fixed_count -->
            <div v-if="isRecurring && !formPrice.is_ongoing">
                <InputLabel for="fixed_count" value="Number of cycles"/>
                <div class="text-gray-500 text-xs">Subscription will charge a customer X times. Last charge will complete the subscription and cancel future billings.</div>
                <TextInput
                        id="fixed_count"
                        v-model="formPrice.fixed_count"
                        type="number"
                        min="1"
                        class="w-20"
                        />
                <InputError :message="formPrice.errors.fixed_count" class="mt-2"/>
            </div>

            <!-- is_renewable -->
            <div v-if="showRenewable">
                <Switcher
                    v-model="formPrice.is_renewable"
                    @update:modelValue="checkIntro('is_renewable', formPrice.is_renewable)"
                    title="Renewable"
                    :description="'Pack is ' + (formPrice.is_renewable ? '' : 'not ') + 'renewable'"/>
                <InputError :message="formPrice.errors.is_renewable" class="mt-2"/>
            </div>

            <!-- is_intro -->
            <div v-if="showIntro">
                <Switcher
                    v-model="formPrice.is_intro"
                    @update:modelValue="checkIntro('is_intro', formPrice.is_intro)"
                    title="Intro pack"
                    :description="'Pack available to ' + (formPrice.is_intro ? 'members who made no purchases in the past' : 'anyone')"/>
                <InputError :message="formPrice.errors.is_intro" class="mt-2"/>
            </div>

            <!-- Location restrictions -->
            <div>
                <InputLabel for="locations" value="Location restrictions"/>
                <Multiselect
                    v-model="formPrice.location_ids"
                    mode="tags"
                    id="locations"
                    :options="locations"
                    :close-on-select="true"
                    :hide-selected="true"
                    placeholder="Select locations"
                    >
                    <template v-slot:option="{ option }">
                        <ColoredValue :color="option.status ? 'green' : 'grey'" :title="option.label" />
                    </template>
                </Multiselect>
                <div class="text-gray-500 text-xs">Leave blank to make this option available for all locations or select locations to restrict option availability.</div>
            </div>

            <!-- is_active -->
            <div v-if="is_new_price">
                <Switcher
                    v-model="formPrice.is_active"
                    title="Pricing option status"
                    :description="formPrice.is_active ? 'Active' : 'Inactive'"/>
                <InputError :message="formPrice.errors.is_active" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="formPrice.recentlySuccessful" class="mr-3">
                Saved
            </ActionMessage>
            <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': formPrice.processing }"
                :disabled="formPrice.processing">
                {{is_new_price ? 'Create new' : 'Save changes'}}
            </ButtonLink>
        </template>
    </FormSection>
</template>

<script setup>
import { reactive, computed, ref } from 'vue';
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from '@/Components/TextArea.vue';
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Switcher from "@/Components/Switcher.vue";
import Multiselect from '@vueform/multiselect';
import Datepicker from '@vuepic/vue-datepicker';
import Tabs from '@/Components/Tabs.vue';
import Tab from '@/Components/Tab.vue';
import { RadioGroup, RadioGroupOption } from '@headlessui/vue';
import '@vueform/multiselect/themes/tailwind.css';
import '@vuepic/vue-datepicker/dist/main.css';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
  faGear
} from '@fortawesome/free-solid-svg-icons';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps({
    pack_types: Object,
    periods: Object,
    price_types: Object,
    classtypes: Object,
    prices: Object,
    default_currency: String,
    form: {
        type: Object,
        required: true
    },
    formPrice: {
        type: Object,
        required: true
    },
    submitted: {
        type: Function,
        required: true
    },
    savePrice: Function,
    editPrice: Function,
    isNew: {
        type: Boolean,
        default: false
    }
});

props.form.restrictions = reactive({
     offpeak: props.form.restrictions?.offpeak ?? false,
     classtypes: props.form.restrictions?.classtypes ?? [],
});

// is_renewable and is_intro can't be ON at the same time
const checkIntro = (el, v) => {
    if(el == 'is_intro' && v){
        props.formPrice.is_renewable = false;
    }
    if(el == 'is_renewable' && v){
        props.formPrice.is_intro = false;
    }
};
const isDefaultType = computed(() => {
  return props.form.type == 'default';
});
const showRestrictions = computed(() => {
  return !isDefaultType.value && props.form.is_restricted;
});
const showFap = computed(() => {
  return !isDefaultType.value && props.form.is_unlimited;
});
const showClassRestrictions = computed(() => {
  return ['class_lesson', 'hybrid'].includes(props.form.type);
});

const showExpiration = computed(() => {
  return !isDefaultType.value && props.formPrice.is_expiring;
});
const isRecurring = computed(() => {
  return props.formPrice.type == 'recurring';
});

const showPriceForm = ref(false);
const active_tab_index = ref(0);
const switchTab = (val) => {
    active_tab_index.value = val;
};
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <Tabs 
                :tabs="['Basics', 'Options', 'Pricing']"
                :active="active_tab_index"
                @tab-changed="switchTab"
                >

                <!-- Tab Basics -->
                <Tab :on="active_tab_index" :index="0" class="space-y-4">
                    <!-- Type -->
                    <InputLabel value="Membership Type"/>
                    <RadioGroup v-model="form.type" class="cursor-pointer space-y-4">
                        <RadioGroupOption 
                            as="template"
                            v-for="option in pack_types"
                            :key="option.value"
                            :value="option.value"
                            v-slot="{ active, checked }"
                            >
                            <div class="">
                                <span class='block sm:inline-block w-28 mr-4 px-5 py-2 rounded-lg text-center transition' :class="checked ? 'font-bold text-white bg-green-700' : 'bg-gray-50'">{{ option.label }}</span>
                                <span class='transition' :class="checked ? '' : 'text-gray-400'">{{ option.description }}</span>
                            </div>
                        </RadioGroupOption>
                    </RadioGroup>
                    <InputError :message="form.errors.type" class="mt-2"/>

                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Title"/>
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            />
                        <InputError :message="form.errors.title" class="mt-2"/>
                    </div>
                    <!-- Sub title -->
                    <div>
                        <InputLabel for="sub_title" value="Sub title"/>
                        <div class="text-gray-500 text-xs">optional</div>
                        <TextInput
                            id="sub_title"
                            v-model="form.sub_title"
                            type="text"
                            class="mt-1 block w-full"
                            />
                        <InputError :message="form.errors.sub_title" class="mt-2"/>
                    </div>
                    <!-- Description -->
                    <div>
                        <InputLabel for="description" value="Description"/>
                        <div class="text-gray-500 text-xs">optional</div>
                        <TextInput
                            id="description"
                            v-model="form.description"
                            type="text"
                            class="mt-1 block w-full"
                            />
                        <InputError :message="form.errors.description" class="mt-2"/>
                    </div>
                </Tab>

                <!-- Tab Options -->
                <Tab :on="active_tab_index" :index="1" class="space-y-4">
                    <!-- is_active -->
                    <div>
                        <Switcher
                            v-model="form.is_active"
                            :title="'Status'"
                            description="Only active packs will be available for purchase"/>
                        <InputError :message="form.errors.is_active" class="mt-2"/>
                    </div>

                    <!-- is_unlimited => ONLY WHEN TYPE IS NOT ['default'] -->
                    <div v-if="!isDefaultType">
                        <Switcher
                            v-model="form.is_unlimited"
                            title="Unlimited pack"
                            description="Members can make unlimited bookings when this option enabled"/>
                        <InputError :message="form.errors.is_unlimited" class="mt-2"/>
                    </div>

                    <!-- is_fap => ONLY WHEN TYPE IS NOT ['default'] and is_unlimited -->
                    <div v-if="showFap">
                        <Switcher
                            v-model="form.is_fap"
                            title="Fair access policy"
                            description="Enable FAP to prevent booking abuse on unlimited packs. With FAP you may, for example, limit member to 2 classes per day."/>
                        <InputError :message="form.errors.is_fap" class="mt-2"/>
                    </div>

                    <!-- is_restricted -->
                    <div v-if="!isDefaultType">
                        <Switcher
                            v-model="form.is_restricted"
                            title="Restrictions"
                            :description="'Pack has ' + (form.is_restricted ? '' : 'no ') + 'restrictions'"/>
                        <InputError :message="form.errors.is_restricted" class="mt-2"/>
                    </div>

                    <!-- conditional restrictions -->
                    <div v-if="showRestrictions" class="space-y-4">
                        <!-- restrictions.offpeak -->
                        <Switcher
                            v-model="props.form.restrictions.offpeak"
                            title="Off Peak"
                            description="Pack sessions can be used for off peak classes"/>
                        <div v-if="showClassRestrictions">
                            <!-- restrictions.classtypes => ONLY WHEN TYPE IS IN ['class_lesson', 'hybrid'] -->
                            <InputLabel for="classtypes" value="Class type restrictions"/>
                            <Multiselect
                                v-model="props.form.restrictions.classtypes"
                                mode="multiple"
                                id="classtypes"
                                :options="classtypes"
                                :searchable="true"
                                :close-on-select="false"
                                :hide-selected="false"
                                placeholder="Select types"
                                >
                            </Multiselect>
                            <!-- restrictions.servicetypes => ONLY WHEN TYPE IS IN ['service', 'hybrid'] -->
                        </div>
                    </div>

                    <!-- is_private -->
                    <div>
                        <Switcher
                            v-model="form.is_private"
                            title="Private pack"
                            :description="'Pack visible ' + (form.is_private ? 'via private link' : 'to anyone')"/>
                        <InputError :message="form.errors.is_private" class="mt-2"/>
                    </div>

                    <!-- private_url -->
                    <div v-if="form.is_private" class="col-span-6 sm:col-span-4">
                        <InputLabel for="private_url" value="Private url"/>
                        <TextInput
                            id="private_url"
                            v-model="form.private_url"
                            type="text"
                            class="mt-1 block w-full"
                            />
                        <InputError :message="form.errors.private_url" class="mt-2"/>
                        <p>Private link will be: <b>https://{{$page.props.app_domain}}.{{$page.props.business_seetings.subdomain}}/some-route/{{form.private_url}}</b></p>
                    </div>

                    <!-- active dates -->
                    <div class="gap-8 md:flex">
                        <!-- active_from -->
                        <div>
                            <InputLabel for="active_from" value="Active from"/>
                            <div>When date is set, pack will not be available for sale <b>before</b> this date.</div>
                            <Datepicker
                                class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300
                                        focus:bg-white focus:ring-0"
                                v-model="form.active_from"
                                :enable-time-picker="false"
                                :format="$page.props.business_seetings.date_format.format_js"
                                placeholder="Leave blank to ignore"
                                text-input
                                auto-apply
                                />
                            <InputError :message="form.errors.active_from" class="mt-2"/>
                        </div>

                        <!-- active_to -->
                        <div>
                            <InputLabel for="active_to" value="Active to"/>
                            <div>When date is set, pack will not be available for sale <b>after</b> this date.</div>
                            <Datepicker
                                class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300
                                        focus:bg-white focus:ring-0"
                                v-model="form.active_to"
                                :enable-time-picker="false"
                                :format="$page.props.business_seetings.date_format.format_js"
                                placeholder="Leave blank to ignore"
                                text-input
                                auto-apply
                                />
                            <InputError :message="form.errors.active_to" class="mt-2"/>
                        </div>
                    </div>
                </Tab>

                <!-- Tab Pricing -->
                <Tab :on="active_tab_index" :index="2" class="space-y-4">
                    <template v-if="isNew">
                        <h2 class="text-xl">Please create a pack first in order to add pricing options</h2>
                    </template>
                    <template v-else>
                        <h2 class="text-2xl">Pricing options ({{prices.length}})</h2>
                        <div v-if="prices.length" class="flex flex-wrap gap-4">
                            <div v-for="price in prices" class="shadow-md w-80">
                                <div class="flex font-medium items-center justify-between space-between" :class="price.is_active ? 'bg-green-400' : 'bg-gray-400'">
                                    <div class="p-2">{{price_types.find(element => element.value == price.type).label }}</div>
                                    <Dropdown align="right" width="48" :content-classes="['bg-gray-100', 'p-1', 'space-y-4']">
                                        <template #trigger>
                                        <font-awesome-icon :icon="faGear" class="cursor-pointer p-2" />
                                        </template>
                                        <template #content>
                                            <button @click="editPrice('toggle', price.id)" type="button" class="block" :class="price.is_active ? 'text-gray-400' : 'text-green-400'"> {{price.is_active ? 'Deactive' : 'Make active'}} </button>
                                            <button @click="editPrice('delete', price.id)" type="button" class="block text-red-500"> Delete </button>
                                        </template>
                                    </Dropdown>
                                </div>
                                <div class="p-2">
                                    <div>ID: {{price.id}}</div>
                                    <div class="font-bold">{{price.price}} {{price.currency.toUpperCase()}}</div>
                                    <div>{{'Pack is ' + (price.is_renewable ? '' : 'not ') + 'renewable'}}</div>
                                    <div>{{'Pack is ' + (price.is_intro ? 'intro' : 'not intro')}}</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="block text-green-500" @click="showPriceForm = !showPriceForm">
                            Add new option
                        </button>

                        <!-- NEW PRICE OPTION -->
                        <div v-if="showPriceForm" class="border border-dashed border-green-500 p-2 space-y-4">

                            <!-- Type -->
                            <div class="flex">
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
                            <div>
                                <InputLabel for="price" value="Amount" />
                                <TextInput
                                    id="price"
                                    v-model="formPrice.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="mt-1"
                                />
                                <span class="ml-1">{{default_currency.toUpperCase()}}</span>
                                <InputError :message="formPrice.errors.price" class="mt-2" />
                            </div>

                            <!-- Sessions -->
                            <div v-if="!isDefaultType">
                                <InputLabel for="price" value="Session credits" />
                                <div class="text-gray-500 text-xs">Member will receive this amount of session credits after the charge or checkout. Credits can be used to make bookings.</div>
                                <TextInput
                                    id="price"
                                    v-model="formPrice.sessions"
                                    type="number"
                                    min="1"
                                    step="1"
                                    class="mt-1"
                                />
                                <InputError :message="formPrice.errors.sessions" class="mt-2" />
                            </div>

                            <!-- is_expiring (session credits) -->
                            <div v-if="!isDefaultType">
                                <Switcher
                                    v-model="formPrice.is_expiring"
                                    title="Session credits expiration"
                                    :description="'Pack sessions ' + (formPrice.is_expiring ? 'will' : 'never') + ' expire (since day of creation)'"/>
                                <InputError :message="formPrice.errors.is_expiring" class="mt-2"/>
                            </div>

                            <div v-if="showExpiration">
                                <InputLabel for="expiration" value="In"/>
                                <div class="flex mt-1 gap-2">
                                    <!-- Expiry integer value -->
                                    <TextInput
                                        id="expiration"
                                        v-model="formPrice.expiration"
                                        type="number"
                                        min="1"
                                        class="block"
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
                                <div v-if="isRecurring" class="text-gray-500 text-xs">You can match session credits to be inline with your billing cycle or make them expire before/after next billing cycle.</div>
                                <div v-else class="text-gray-500 text-xs">Session credits will expire on next day of selected period. The lifecycle of session credit starts on the checkout day.</div>
                                <InputError :message="formPrice.errors.expiration" class="mt-2"/>
                                <InputError :message="formPrice.errors.expiration_period" class="mt-2"/>
                            </div>

                            <!-- Recurring interval -->
                            <div v-if="isRecurring">
                                <InputLabel for="interval_count" value="Billing interval"/>
                                <div class="flex mt-1 gap-2">
                                    <!-- interval integer value -->
                                    <TextInput
                                        id="interval_count"
                                        v-model="formPrice.interval_count"
                                        type="number"
                                        min="1"
                                        class="block"
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

                            <!-- is_ongoing -->
                            <div v-if="isRecurring">
                                <Switcher
                                    v-model="formPrice.is_ongoing"
                                    title="Subscription duration"
                                    :description="formPrice.is_ongoing ? 'Subscrption runs indefinitely, unless cancelled' : 'Subscrption should complete after specified number of billing cycles'"/>
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
                                        class="block"
                                        />
                                <InputError :message="formPrice.errors.fixed_count" class="mt-2"/>
                            </div>

                            <!-- is_renewable -->
                            <div v-if="!isRecurring">
                                <Switcher
                                    v-model="formPrice.is_renewable"
                                    @update:modelValue="checkIntro('is_renewable', formPrice.is_renewable)"
                                    title="Renewable"
                                    :description="'Pack is ' + (formPrice.is_renewable ? '' : 'not ') + 'renewable'"/>
                                <InputError :message="formPrice.errors.is_renewable" class="mt-2"/>
                            </div>

                            <!-- is_intro -->
                            <div v-if="!isRecurring">
                                <Switcher
                                    v-model="formPrice.is_intro"
                                    @update:modelValue="checkIntro('is_intro', formPrice.is_intro)"
                                    title="Intro pack"
                                    :description="'Pack available to ' + (formPrice.is_intro ? 'members who made no purchases in the past' : 'anyone')"/>
                                <InputError :message="formPrice.errors.is_intro" class="mt-2"/>
                            </div>

                            <!-- is_active -->
                            <div>
                                <Switcher
                                    v-model="formPrice.is_active"
                                    title="Pricing option status"
                                    :description="formPrice.is_active ? 'Active' : 'Inactive'"/>
                                <InputError :message="formPrice.errors.is_active" class="mt-2"/>
                            </div>

                            <PrimaryButton 
                                @click="savePrice"
                                type="button"
                                :class="{ 'opacity-25': formPrice.processing }"
                                :disabled="formPrice.processing"
                                >Create new option</PrimaryButton>
                        </div>
                    </template>
                </Tab>
            </Tabs>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span v-if="isNew">Create</span>
                <span v-else>Save changes</span>
            </PrimaryButton>
        </template>
    </FormSection>
</template>

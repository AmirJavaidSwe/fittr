<script setup>
import { reactive, computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from '@/Components/TextArea.vue';
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import Switcher from "@/Components/Switcher.vue";
import Multiselect from '@vueform/multiselect';
import Datepicker from '@vuepic/vue-datepicker';
import SideModal from "@/Components/SideModal.vue";
import PriceCard from "../PackPrice/Card.vue";
import PriceForm from "../PackPrice/Form.vue";
import { faCircle, faCircleDot } from '@fortawesome/free-regular-svg-icons';
import { RadioGroup, RadioGroupOption } from '@headlessui/vue';
import '@vueform/multiselect/themes/tailwind.css';
import '@vuepic/vue-datepicker/dist/main.css';
import draggable from 'vuedraggable';

const props = defineProps({
    pack_types: Array,
    periods: Array,
    price_types: Array,
    locations: Array,
    classtypes: Object,
    servicetypes: Object,
    pack_prices: Array,
    default_currency: String,
    form: {
        type: Object,
        required: true
    },
    submitted: {
        type: Function,
        required: true
    },
    toggleOrDeletePrice: Function,
    isNew: {
        type: Boolean,
        default: false
    },
    pack_id: Number
});

// draggable sorting order
const sortPacks = (e) => {
    console.log(sorted.value)
    axios.post(route("partner.packs.price.sort"), sorted.value);
};
const sorted = computed(() => {
  return props.pack_prices.map((pack_price, index) => {
        return {id: pack_price.id, ordering: index};
    });
})

props.form.restrictions = reactive({
     offpeak: props.form.restrictions?.offpeak ?? false,
     classtypes: props.form.restrictions?.classtypes ?? [],
     servicetypes: props.form.restrictions?.servicetypes ?? [],
});

const isPassType = computed(() => {
  return props.form.type == 'location_pass';
});
const showRestrictions = computed(() => {
  return props.form.is_restricted;
});
const showClassRestrictions = computed(() => {
  return props.form.is_restricted && ['class_lesson', 'hybrid'].includes(props.form.type);
});
const showServiceRestrictions = computed(() => {
  return props.form.is_restricted && ['service', 'hybrid'].includes(props.form.type);
});

const subDomain = computed(() => usePage().props.business_settings?.subdomain);
const privateLinkUrl = computed(() => {
    if(!props.form.private_url || !subDomain) return;
    return route('ss.memberships.private', { subdomain: subDomain.value, url: props.form.private_url });
});

//edit existing price start//
const default_price_fields = {
    priceable_id: props.pack_id, //id of the pack
    type: 'one_time',
    is_active: true,
    price: null,
    sessions: 0,
    is_expiring: true,
    expiration: null,
    expiration_period: null,
    interval: null,
    interval_count: null,
    min_term: null,
    is_unlimited: false,
    is_fap: false,
    fap_value: null,
    is_ongoing: true,
    fixed_count: null,
    is_renewable: true,
    is_intro: false,
    location_ids: [],
};
const shown_price_form = ref(false);
const is_new_price = ref(false);
const price_edited = ref(null);
const showEditPrice = (price) => {
    shown_price_form.value = !shown_price_form.value;
    price_edited.value = price ?? default_price_fields;
    is_new_price.value = price === undefined;
};
//edit existing price end//
</script>

<template>
    <FormSection @submitted="submitted">
        <template #title>
            Pack details
        </template>
        <template #description>
            Update pack details and pricing options
        </template>
        <template #form>
            <!-- Tab Basics -->
            <!-- Type -->
            <InputLabel value="Membership Type"/>
            <div v-if="isNew" class="text-gray-500 text-xs">Membership type can be changed to Class, Service or Hybrid. Pass or Corporate type can not be changed once created.</div>
            <RadioGroup v-model="form.type" class="cursor-pointer flex flex-wrap gap-4">
                <RadioGroupOption 
                    as="template"
                    v-for="option in pack_types"
                    :key="option.value"
                    :value="option.value"
                    v-slot="{ active, checked }"
                    >
                    <div class="p-2 transition rounded-md w-full md:w-80 lg:flex-1 bg-gray-50 border" :class="{ 'bg-primary-100/75 border-primary-500': checked }">
                        <div class='flex font-bold items-center justify-between text-2xl mb-4' :class="{ 'text-primary-500': checked }">
                            {{ option.label }}
                            <font-awesome-icon v-if="checked" :icon="faCircleDot" />
                            <font-awesome-icon v-else :icon="faCircle" />
                        </div>
                        <div class='transition'>{{ option.description }}</div>
                    </div>
                </RadioGroupOption>
            </RadioGroup>
            <InputError :message="form.errors.type" class="mt-2"/>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-4">
                    <!-- Title -->
                    <div>
                        <InputLabel for="title" value="Title"/>
                        <div class="text-gray-500 text-xs">required</div>
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
                        <TextInput
                            id="sub_title"
                            v-model="form.sub_title"
                            type="text"
                            class="mt-1 block w-full"
                            />
                        <InputError :message="form.errors.sub_title" class="mt-2"/>
                    </div>
                    </div>
                <!-- Description -->
                <div class="flex flex-col">
                    <InputLabel for="description" value="Description"/>
                    <TextArea
                        id="description"
                        v-model="form.description"
                        type="text"
                        class="flex-grow w-full"
                    />
                    <InputError :message="form.errors.description" class="mt-2"/>
                </div>
            </div>

            <!-- Tab Options -->
            <!-- is_active -->
            <div class="bg-gray-50 p-2 rounded-md">
                <Switcher
                    v-model="form.is_active"
                    :title="'Status'"
                    description="Only active packs will be available for purchase"/>
                <InputError :message="form.errors.is_active" class="mt-2"/>
            </div>

            <!-- is_restricted -->
            <div v-if="!isPassType" class="bg-gray-50 p-2 rounded-md space-y-4">
                <Switcher
                    v-model="form.is_restricted"
                    title="Restrictions"
                    :description="'Pack has ' + (form.is_restricted ? '' : 'no ') + 'restrictions'"/>
                <InputError :message="form.errors.is_restricted" class="mt-2"/>

                <!-- conditional restrictions -->
                <!-- restrictions.offpeak -->
                <div v-if="showRestrictions">
                    <Switcher
                        v-model="props.form.restrictions.offpeak"
                        title="Off Peak"
                        description="Pack sessions can be used for off peak classes"/>
                </div>
                <div v-if="showClassRestrictions">
                    <!-- restrictions.classtypes => ONLY WHEN TYPE IS IN ['class_lesson', 'hybrid'] -->
                    <InputLabel for="classtypes" value="Class type restrictions"/>
                    <Multiselect
                        v-model="props.form.restrictions.classtypes"
                        mode="tags"
                        id="classtypes"
                        :groups="true"
                        :options="[{
                            label: (props.form.restrictions.classtypes.length == Object.values(classtypes).length) ? 'Deselect All' : 'Select All',
                            options: classtypes,
                        }]"

                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="true"
                        :hide-selected="true"
                        placeholder="Select types"
                        >
                    </Multiselect>
                </div>
                <div v-if="showServiceRestrictions">
                    <!-- restrictions.servicetypes => ONLY WHEN TYPE IS IN ['service', 'hybrid'] -->
                    <InputLabel for="servicetypes" value="Service type restrictions"/>
                    <Multiselect
                        v-model="props.form.restrictions.servicetypes"
                        mode="tags"
                        id="servicetypes"
                        :groups="true"
                        :options="[{
                            label: (props.form.restrictions.servicetypes.length == Object.values(servicetypes).length) ? 'Deselect All' : 'Select All',
                            options: servicetypes,
                        }]"

                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="true"
                        :hide-selected="true"
                        placeholder="Select types"
                        >
                    </Multiselect>
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
                <p v-if="privateLinkUrl">Private link will be: <b>{{privateLinkUrl}}</b></p>
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
                        :format="$page.props.business_settings.date_format.format_js"
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
                        :format="$page.props.business_settings.date_format.format_js"
                        placeholder="Leave blank to ignore"
                        text-input
                        auto-apply
                        />
                    <InputError :message="form.errors.active_to" class="mt-2"/>
                </div>
            </div>

            <!-- Tab Pricing -->
            <div class="py-8">
                <div class="border-t border-gray-200" />
            </div>
            <template v-if="isNew">
                <h2 class="text-xl">Please create a pack first in order to add pricing options</h2>
            </template>
            <template v-else>
                <div class="flex flex-wrap items-center justify-between">
                    <h2 class="text-2xl">Pricing options ({{pack_prices.length}})</h2>
                    <ButtonLink
                        styling="primary"
                        size="small"
                        type="button"
                        @click="showEditPrice()"
                        >
                        Add new option
                    </ButtonLink>

                </div>
                <!-- Existing prices list: -->
                <transition-group>
                    <draggable
                        item-key="id"
                        key="transition-group-key"
                        :list="pack_prices"
                        :animation="200"
                        ghostClass="opacity-50"
                        @end="sortPacks"
                        class="flex flex-wrap gap-4"
                        >
                        <template #item="{element}">
                            <PriceCard
                                :key="element.id"
                                :price="element"
                                :pack_type="form.type"
                                :label="price_types.find(el => el.value == element.type).label"
                                @toggle="toggleOrDeletePrice('toggle', element.id)"
                                @edit="showEditPrice(element)"
                                @delete="toggleOrDeletePrice('delete', element.id)"
                            >
                            </PriceCard>
                        </template>
                    </draggable>
                </transition-group>
                <!-- Edit price Modal -->
                <SideModal :show="shown_price_form" @close="shown_price_form = false">
                    <template #title>{{is_new_price ? 'Create new pricing option' : 'Update pricing option'}}</template>

                    <template #content>
                        <PriceForm
                            :pack_types="pack_types"
                            :periods="periods"
                            :price_types="price_types"
                            :locations="locations"
                            :default_currency="default_currency"
                            :pack_type="form.type"
                            :price="price_edited"
                            :is_new_price="is_new_price"
                            :pack_id="pack_id"
                            @created="shown_price_form = false"
                            @updated="shown_price_form = false"
                            >
                            <!-- Slot area for extra content -->
                        </PriceForm>
                    </template>
                </SideModal>
            </template>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span v-if="isNew">Create</span>
                <span v-else>Save changes</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>

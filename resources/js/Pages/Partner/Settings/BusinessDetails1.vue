<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import GeneralSettingsMenu from "@/Pages/Partner/Settings/GeneralSettingsMenu.vue";
import GeneralSettingsVerticalMenu from "@/Pages/Partner/Settings/GeneralSettingsVerticalMenu.vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/tailwind.css";
import CalendarIcon from "@/Icons/CalendarIcon.vue";
import TimeIcon from "@/Icons/TimeIcon.vue";


const props = defineProps({
    countries: Array,
    timezones: Array,
    form_data: Object,
    business_seetings: Object,
});

const business_phone = ref(props.form_data.business_phone);
const testPhone = (e) => {
    setTimeout(() => {
        business_phone.value = e.target.value.replace(/\D+/, "");
    }, 1);
};

const form = useForm({
    business_name: props.form_data.business_name,
    business_email: props.form_data.business_email,
    country_id: props.form_data.country_id,
    business_phone: business_phone.value,
    timezone: props.form_data.timezone,
    show_timezone: props.form_data.show_timezone,
});

const currency = ref("");
const flag = ref("");
const flag_img = ref("");
const mask = ref("");
const dial_code = ref("");
const clearSelectedCountry = () => {
    form.country_id = null;
    countryChanged()
}
const countryChanged = () => {
    let country = props.countries.find(({ id }) => id == form.country_id);
    currency.value = country?.currency;
    flag.value = country?.iso;
    flag_img.value = "/images/flags/" + flag.value + ".svg";
    mask.value = country?.mask;
    dial_code.value = country?.dial_code;
};
onMounted(() => {
    countryChanged();
});

const localtime = computed(() => {
    let local = DateTime.now().setZone(form.timezone);
    return (
        local.toFormat(props.business_seetings.date_format.format_js) +
        " " +
        local.toFormat(props.business_seetings.time_format.format_js) +
        " UTC " +
        local.toFormat("ZZ")
    );
});

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        business_phone: business_phone.value
    })).put(route("partner.settings.general-details"), {
        preserveScroll: true,
    });
};

const countriesOptions = computed(() => {
    const options = props.countries;
    const data = {};
    for (const option in options) {
        const value = options[option];
        data[value.id] = value.name;
    }
    return data;
})
const timezoneOptions = computed(() => {
    const options = props.timezones;
    const data = {};
    for (const option in options) {
        const value = options[option];
        data[value.title] = value.title;
    }
    return data;
});

</script>

<template>
    <FormSection @submitted="submitForm">
        <template #description>
            <GeneralSettingsMenu />
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="business_name" value="Business Name" />
                <TextInput id="business_name" v-model="form.business_name" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.business_name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="business_email" value="Business Email" />
                <TextInput id="business_name" v-model="form.business_email" type="email" class="mt-1 block w-full" />
                <InputError :message="form.errors.business_email" class="mt-2" />
            </div>

            <!-- Country -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="country" value="Country" />
                <Multiselect id="country" v-model="form.country_id" :options="countriesOptions" :searchable="true"
                    @select="countryChanged" @clear="clearSelectedCountry" class="mt-1 block w-full border" />
                <InputError :message="form.errors.country_id" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="bg-gray-100 flex font-medium gap-4 items-center mt-1 text-gray-700 text-sm">
                    <div class="w-20 h-10">
                        <img v-if="flag" :src="flag_img" :alt="flag" class="border h-full" />
                    </div>
                    <div>Default currency: <b v-text="currency"></b></div>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="business_phone" value="Phone Number" />
                <div class="flex gap-2 items-center">
                    <div class="input-field mt-1 px-2 w-14 h-9" v-text="dial_code"></div>
                    <TextInput id="business_phone" v-model="business_phone" type="text" class="mt-1 block w-full"
                        @input="testPhone" maxlength="12" :placeholder="mask" />
                </div>
                <InputError :message="form.errors.business_phone" class="mt-2" />
            </div>

            <!-- Timezone -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="timezone" value="Timezone" />
                <div class="mt-1">
                    Note: Change of timezone will affect your store schedules
                    and all resources using dates.
                </div>
                <Multiselect id="timezone" v-model="form.timezone" :options="timezoneOptions" :searchable="true"
                    class="mt-1 block w-full" />
                <InputError :message="form.errors.timezone" class="mt-2" />
                <div v-if="localtime" class="mt-1">
                    Local time: {{ localtime }}
                </div>
            </div>

            <!-- Show timezone in store -->
            <div class="col-span-6 sm:col-span-4">
                <Switcher v-model="form.show_timezone" title="Timezone in Service Store"
                    description="Enable this option to inform visitors about business time zone." />
                <InputError :message="form.errors.show_timezone" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                Save
            </ButtonLink>
        </template>
    </FormSection>


    <GeneralSettingsMenu />

    <FormSectionVertical>
        <template #tabsList>
            <GeneralSettingsVerticalTabs />
        </template>
        <template #heading>
            <h3 class="text-xl pt-3 pb-3"><strong>Business Details</strong></h3>
        </template>
        <template #form>
            <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="business_name" value="Business Name" />
                    <TextInput id="business_name" v-model="form.business_name" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.business_name" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="business_email" value="Business Email" />
                    <TextInput id="business_name" v-model="form.business_email" type="email" class="mt-1 block w-full" />
                    <InputError :message="form.errors.business_email" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="country" value="Country" />
                    <Multiselect id="country" v-model="form.country_id" :options="countriesOptions" :searchable="true"
                        @select="countryChanged" @clear="clearSelectedCountry" class="mt-1 block w-full border" />
                    <InputError :message="form.errors.country_id" class="mt-2" />
                </div>
                <span class="block text-gray-400 mt-5">Default Currency</span>
                <!-- Phone Number -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="business_phone" value="Phone Number" />
                    <div class="flex gap-2 items-center">
                        <div class="input-field mt-1 px-2 w-14 h-9" v-text="dial_code"></div>
                        <TextInput id="business_phone" v-model="business_phone" type="text" class="mt-1 block w-full"
                            @input="testPhone" maxlength="12" :placeholder="mask" />
                    </div>
                    <InputError :message="form.errors.business_phone" class="mt-2" />
                </div>
                <!-- Timezone -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="timezone" value="Timezone" />

                    <Multiselect id="timezone" v-model="form.timezone" :options="timezoneOptions" :searchable="true"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.timezone" class="mt-2" />
                    <div class="mt-1">
                        Note: Change of timezone will affect your store schedules
                        and all resources using dates.
                    </div>
                </div>
                <div class="bg-mainBg col-span-6 sm:col-span-4 mt-4 p-5 rounded mt-6">
                    <Switcher v-model="form.show_timezone" title="Timezone in Service Store"
                        description="Enable this option to inform visitors about business time zone." />
                    <InputError :message="form.errors.show_timezone" class="mt-2" />
                </div>
                <div class="flex mt-5">
                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Save
                    </ButtonLink>
                </div>
        </template>
    </FormSectionVertical>
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col">
            <div class="row-span-7 col-span-7 border-l-2 border-gray-200 p-5">
                <h3 class="text-xl pt-3 pb-3"><strong>Business Details</strong></h3>
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="business_name" value="Business Name" />
                    <TextInput id="business_name" v-model="form.business_name" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.business_name" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="business_email" value="Business Email" />
                    <TextInput id="business_name" v-model="form.business_email" type="email" class="mt-1 block w-full" />
                    <InputError :message="form.errors.business_email" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="country" value="Country" />
                    <Multiselect id="country" v-model="form.country_id" :options="countriesOptions" :searchable="true"
                        @select="countryChanged" @clear="clearSelectedCountry" class="mt-1 block w-full border" />
                    <InputError :message="form.errors.country_id" class="mt-2" />
                </div>
                <span class="block text-gray-400 mt-5">Default Currency</span>
                <!-- Phone Number -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="business_phone" value="Phone Number" />
                    <div class="flex gap-2 items-center">
                        <div class="input-field mt-1 px-2 w-14 h-9" v-text="dial_code"></div>
                        <TextInput id="business_phone" v-model="business_phone" type="text" class="mt-1 block w-full"
                            @input="testPhone" maxlength="12" :placeholder="mask" />
                    </div>
                    <InputError :message="form.errors.business_phone" class="mt-2" />
                </div>
                <!-- Timezone -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="timezone" value="Timezone" />

                    <Multiselect id="timezone" v-model="form.timezone" :options="timezoneOptions" :searchable="true"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.timezone" class="mt-2" />
                    <div class="mt-1">
                        Note: Change of timezone will affect your store schedules
                        and all resources using dates.
                    </div>
                </div>
                <div class="bg-mainBg col-span-6 sm:col-span-4 mt-4 p-5 rounded mt-6">
                    <Switcher v-model="form.show_timezone" title="Timezone in Service Store"
                        description="Enable this option to inform visitors about business time zone." />
                    <InputError :message="form.errors.show_timezone" class="mt-2" />
                </div>
                <div class="flex mt-5">
                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Save
                    </ButtonLink>
                </div>
            </div>
        </div>
    </div>
    <!--Legal Address-->
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col">
            <div class="row-span-2 hidden md:block col-span-1 p-5">
                <ul class="vertical-tabs">
                    <li>
                        <a class="p-2 block hover:underline transition " href="#">Business Details</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition active" href="#">Legal Address</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">Format</a>
                    </li>
                </ul>
            </div>
            <div class="row-span-7 col-span-7 border-l-2 border-gray-200 p-5">
                <h3 class="text-xl pt-3 pb-3"><strong>Legal Address</strong></h3>
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="address_line1" value="Address Line 1" />
                    <TextInput id="address_line1" v-model="form.address_line1" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.address_line1" class="mt-2" />
                </div>

                <!-- Address Line 2 -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="address_line2" value="Address Line 2" />
                    <TextInput id="address_line2" v-model="form.address_line2" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.address_line2" class="mt-2" />
                </div>

                <!-- City -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="city" value="City" />
                    <TextInput id="city" v-model="form.city" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.city" class="mt-2" />
                </div>

                <!-- State -->
                <div v-if="show_states" class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="state" value="State" />
                    <TextInput id="state" v-model="form.state" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.state" class="mt-2" />
                </div>

                <!-- Zip -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="zip_code" value="ZIP Code" />
                    <TextInput id="city" v-model="form.zip_code" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.zip_code" class="mt-2" />
                </div>

                <!-- Country -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <InputLabel for="legal_country_id" value="Country" />
                    <Multiselect id="legal_country_id" v-model="form.legal_country_id" :options="countriesOptions"
                        :searchable="true" @select="countryChanged" @clear="clearSelectedCountry"
                        class="mt-1 block w-full" />

                    <InputError :message="form.errors.legal_country_id" class="mt-2" />
                </div>
                <div class="flex mt-5">

                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Save
                    </ButtonLink>
                </div>
            </div>
        </div>
    </div>
    <!--Formats-->
    <!--Legal Address-->
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col">
            <div class="row-span-2 hidden md:block col-span-1 p-5">
                <ul class="vertical-tabs">
                    <li>
                        <a class="p-2 block hover:underline transition " href="#">Business Details</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition " href="#">Legal Address</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition active" href="#">Format</a>
                    </li>
                </ul>
            </div>
            <div class="row-span-7 col-span-7 border-l-2 border-gray-200 p-5">
                <h3 class="text-xl pt-3 pb-3"><strong>Legal Address</strong></h3>
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <div class="relative">
                        <span class="absolute top-8 left-2">
                            <CalendarIcon />
                        </span>
                        <InputLabel for="date_format" value="Date Format" />
                        <SelectInput id="date_format" v-model="form.date_format" :options="props.formats_date"
                            option_value="id" option_text="example" class="mt-1 block w-full">
                        </SelectInput>
                    </div>
                    <InputError :message="form.errors.date_format" class="mt-2" />
                    <div v-if="date_notes" v-text="date_notes" class="mt-2"></div>
                </div>

                <!-- Time Format -->
                <div class="col-span-6 sm:col-span-4 mt-4">
                    <div class="relative">
                        <span class="absolute top-8 left-2">
                            <TimeIcon />
                        </span>
                        <InputLabel for="time_format" value="Time Format" />
                        <SelectInput id="time_format" v-model="form.time_format" :options="props.formats_time"
                            option_value="id" option_text="example" class="mt-1 block w-full">
                        </SelectInput>
                    </div>
                    <InputError :message="form.errors.time_format" class="mt-2" />
                    <div v-if="time_notes" v-text="time_notes" class="mt-2"></div>
                </div>




                <div class="flex mt-5">

                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Save
                    </ButtonLink>
                </div>
            </div>
        </div>
    </div>
</template>

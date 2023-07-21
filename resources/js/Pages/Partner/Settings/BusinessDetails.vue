<script setup>
import { watch, watchEffect, computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import GeneralSettingsMenu from "@/Pages/Partner/Settings/GeneralSettingsMenu.vue";
import GeneralSettingsVerticalTabs from "@/Pages/Partner/Settings/GeneralSettingsVerticalTabs.vue";
import FormSection from "@/Components/FormSection.vue";
import FormSectionVertical from "@/Components/FormSectionVertical.vue";
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
import intlTelInput from "intl-tel-input";
import "intl-tel-input/build/css/intlTelInput.css";
import { parsePhoneNumber, AsYouType } from "libphonenumber-js";

const props = defineProps({
    countries: Array,
    timezones: Array,
    form_data: Object,
    business_seetings: Object,
});

const business_phone = ref(null);
business_phone.value = '+' + props.form_data.business_phone

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
    countryChanged();
};
const countryChanged = () => {
    let country = props.countries.find(({ id }) => id == form.country_id);
    currency.value = country?.currency;
    flag.value = country?.iso;
    flag_img.value = "/images/flags/" + flag.value + ".svg";
    mask.value = country?.mask;
    dial_code.value = country?.dial_code;
};

watch(business_phone, (newVal, oldVal) => {
    formatPhoneInput();
});
onMounted(async () => {
    countryChanged();
    const input = document.querySelector("#business_phone");
    intlTelInput(input, {
        utilsScript: await import("intl-tel-input/build/js/utils.js"),
        autoInsertDialCode: true,
        formatOnDisplay: true,
        nationalMode: false,
        customContainer: "w-full",
    });
    formatPhoneInput();
});
const formatPhoneInput = () => {
    setTimeout(() => {
        business_phone.value = business_phone.value.replace(/\D+/, "");
        if (!business_phone.value || !business_phone.value.startsWith("+")) {
            business_phone.value = "+" + business_phone.value;
        }
        let letFormattedNumber = "";
        try {
            const newNumber = parsePhoneNumber(business_phone.value);
            letFormattedNumber = new AsYouType().input(business_phone.value);
            business_phone.value = letFormattedNumber
                ? letFormattedNumber
                : business_phone.value;
            } catch (error) {
                business_phone.value = business_phone.value.replace(/\D+/, "");
            }
    }, 100);
};

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
            .replace(/\s/g, "")
            .replace(/\+/g, ""),
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
});
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
    <GeneralSettingsMenu class="lg:hidden" />
    <FormSectionVertical @submitted="submitForm">
        <template #tabsList>
            <GeneralSettingsVerticalTabs />
        </template>
        <template #heading>
            <h3 class="text-2xl pt-3 pb-3 font-bold">Business Details</h3>
        </template>
        <template #form>
            <div class="col-span-12 mt-4">
                <InputLabel for="business_name" value="Business Name" />
                <TextInput
                    id="business_name"
                    v-model="form.business_name"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.business_name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-12 mt-4">
                <InputLabel for="business_email" value="Business Email" />
                <TextInput
                    id="business_name"
                    v-model="form.business_email"
                    type="email"
                    class="mt-1 block w-full"
                />
                <InputError
                    :message="form.errors.business_email"
                    class="mt-2"
                />
            </div>
            <div class="col-span-12 mt-4">
                <InputLabel for="country" value="Country" />
                <Multiselect
                    id="country"
                    v-model="form.country_id"
                    :options="countriesOptions"
                    :searchable="true"
                    @select="countryChanged"
                    @clear="clearSelectedCountry"
                    class="mt-1 block w-full border"
                />
                <InputError :message="form.errors.country_id" class="mt-2" />
            </div>
            <div class="col-span-12 mt-4">
                <div
                    class="bg-gray-100 flex font-medium gap-4 items-center mt-1 text-gray-700 text-sm"
                >
                    <div class="w-20 h-10">
                        <img
                            v-if="flag"
                            :src="flag_img"
                            :alt="flag"
                            class="border h-full"
                        />
                    </div>
                    <div>Default currency: <b v-text="currency"></b></div>
                </div>
            </div>
            <!-- Phone Number -->
            <div class="col-span-12 mt-4">
                <InputLabel for="business_phone" value="Phone Number" />
                <TextInput
                    id="business_phone"
                    v-model="business_phone"
                    type="text"
                    maxlength="16"
                    class="mt-1 block w-full"
                />
                <InputError
                    :message="form.errors.business_phone"
                    class="mt-2"
                />
            </div>
            <!-- Timezone -->
            <div class="col-span-12 mt-4">
                <InputLabel for="timezone" value="Timezone" />

                <Multiselect
                    id="timezone"
                    v-model="form.timezone"
                    :options="timezoneOptions"
                    :searchable="true"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.timezone" class="mt-2" />
                <span class="col-span-1 mt-1">
                    Note:
                    <span class="font-semibold"
                        >Change of timezone will affect your store schedules and
                        all resources using dates.</span
                    >
                </span>
            </div>
            <div class="bg-mainBg col-span-12 mt-4 p-5 rounded mt-6">
                <Switcher
                    v-model="form.show_timezone"
                    title="Timezone in Service Store"
                    description="Enable this option to inform visitors about business time zone."
                    description-left-border
                />
                <InputError :message="form.errors.show_timezone" class="mt-2" />
            </div>
        </template>
        <template #actions>
            <div class="flex mt-5">
                <ActionMessage
                    :on="form.recentlySuccessful"
                    class="font-semibold mr-3 mt-3"
                >
                    Saved.
                </ActionMessage>
                <ButtonLink
                    styling="secondary"
                    size="default"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Save
                </ButtonLink>
            </div>
        </template>
    </FormSectionVertical>
</template>

<script setup>
import { computed, ref } from "vue";
import { Head, Link, useForm } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import LogoLetter from '@/Components/LogoLetter.vue';
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/tailwind.css";

const props = defineProps({
    countries: Array,
    timezones: Array,
    date_format: {
        type: Number,
        required: true,
    },
    time_format: {
        type: Number,
        required: true,
    },
    partner: {
        type: Object,
        required: true,
    },
});
const form = useForm({
    business_name: null,
    business_email: null,
    country_id: null,
    business_phone: null,
    timezone: null,
    subdomain: null,
    date_format: props.date_format,
    time_format: props.time_format,
});
const currency = ref(null);
const flag = ref(null);
const flag_img = ref(null);
const mask = ref(null);
const dial_code = ref(null);
const countriesOptions = computed(() => {
    const data = {};
    props.countries.forEach(function (item) {
        data[item.id] = item.name;
    });
    return data;
});
const timezoneOptions = computed(() => {
    const data = {};
    props.timezones.forEach(function (item) {
        data[item.title] = item.title;
    });
    return data;
});
const clearSelectedCountry = () => {
    form.country_id = null;
    countryChanged();
}
const countryChanged = () => {
    let country = props.countries.find(({ id }) => id == form.country_id);
    currency.value = country?.currency;
    flag.value = country?.iso;
    flag_img.value = "/images/flags/" + flag.value + ".svg";
    mask.value = country?.mask;
    dial_code.value = country?.dial_code;
};
const business_phone = ref(null);
const testPhone = () => {
    setTimeout(() => {
        business_phone.value = business_phone.value.replace(/\D+/, "");
    }, 1);
};
const localtime = computed(() => {
    return DateTime.now().setZone(form.timezone).toLocaleString(DateTime.DATETIME_FULL);
});
const submitForm = () => {
    form.transform((data) => ({
        ...data,
        business_phone: business_phone.value
    })).post(route("partner.onboarding.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head>
        <title>Onboarding</title>
    </Head>
    <div class="bg-gray-100 p-4 h-fit">
        <div class="max-w-md mx-auto">
            <FormSection @submitted="submitForm">
                <template #title>
                    <div>Onboarding</div>
                </template>
                <template #description>
                    <div>Please fill out a few details about your business</div>
                </template>

                <template #form>
                    <!-- Name -->
                    <div>
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
                    <div>
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

                    <!-- Country -->
                    <div>
                        <InputLabel for="country" value="Business country of operations" />
                        <Multiselect
                            id="country"
                            v-model="form.country_id"
                            :options="countriesOptions"
                            :searchable="true"
                            @select="countryChanged"
                            @clear="clearSelectedCountry"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.country_id" class="mt-2" />
                    </div>

                    <div v-if="form.country_id">
                        <div class="bg-gray-100 flex font-medium gap-4 items-center mt-1 text-gray-700 text-sm">
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
                    <div v-if="dial_code">
                        <InputLabel for="business_phone" value="Business Phone Number" />
                        <div class="flex gap-2 items-center">
                            <div class="input-field mt-1 px-2" v-text="dial_code"></div>
                            <TextInput
                                id="business_phone"
                                v-model="business_phone"
                                type="text"
                                class="mt-1 block w-full"
                                @input="testPhone"
                                maxlength="12"
                                :placeholder="mask"
                            />
                        </div>
                        <InputError
                            :message="form.errors.business_phone"
                            class="mt-2"
                        />
                    </div>

                    <!-- Timezone -->
                    <div>
                        <InputLabel for="timezone" value="Business Timezone" />
                        <Multiselect
                            id="timezone"
                            v-model="form.timezone"
                            :options="timezoneOptions"
                            :searchable="true"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.timezone" class="mt-2" />
                        <div v-if="localtime" class="mt-1 text-sm">
                            Local time: {{ localtime }}
                        </div>
                    </div>

                    <!-- subdomain -->
                    <div>
                        <InputLabel for="subdomain" value="Service Store URL" />
                        <div class="mt-1 text-sm">
                            Please type a subdomain of your choice below.<br>
                            Resulted URL will be your public service store.
                        </div>
                        <TextInput
                            id="subdomain"
                            v-model="form.subdomain"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.subdomain" class="mt-2" />
                        <InputError :message="form.errors.unique" class="mt-2" />
                        <p v-if="form.subdomain">
                            <div class=" mt-2 p-1 bg-gray-100">https://{{ form.subdomain }}.{{$page.props.app_domain}}</div>
                        </p>
                    </div>
                </template>

                <template #actions>
                    <div class="flex justify-between w-full">
                        <Link
                            :href="route('logout')"
                            as="button"
                            method="post"
                            class="text-gray-500 hover:text-gray-900 transition font-semibold"
                            >
                            Log out
                        </Link>
                        <ButtonLink
                            styling="secondary"
                            size="default"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Save
                        </ButtonLink>
                    </div>
                    <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                        Saved.
                    </ActionMessage>
                </template>
            </FormSection>
        </div> 
    </div> 
</template>

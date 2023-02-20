<script setup>
import { ref, onMounted  } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import GeneralSettingsMenu from '@/Pages/Partner/Settings/GeneralSettingsMenu.vue';

import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from "@/Components/SelectInput.vue";
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    countries: Array,
    form_data: Object,
});

const form = useForm({
    business_name: props.form_data.business_name,
    business_email: props.form_data.business_email,
    country_id: props.form_data.country_id,
});

const currency = ref('');
const flag = ref('');
const flag_img = ref('');
const countryChanged = () => {
    let country = props.countries.find(({ id }) => id == form.country_id);
    currency.value = country?.currency;
    flag.value = country?.iso;
    flag_img.value = '/images/flags/' + flag.value + '.svg';
};
onMounted(() => {
    countryChanged();
})

const submitForm = () => {
    form.put(route('partner.settings.general-details.update'), {
        preserveScroll: true
    });
};
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
                <TextInput
                    id="business_name"
                    v-model="form.business_name"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.business_name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="business_email" value="Business Email" />
                <TextInput
                    id="business_name"
                    v-model="form.business_email"
                    type="email"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.business_email" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="country" value="Country" />
                <SelectInput
                    id="country"
                    v-model="form.country_id"
                    :options="props.countries"
                    option_value="id"
                    option_text="name"
                    class="mt-1 block w-full"
                    @change="countryChanged"
                >
                </SelectInput>
                <InputError :message="form.errors.country_id" class="mt-2" />

            </div>
            <div class="col-span-6 sm:col-span-4">
                <div class="bg-gray-100 flex font-medium gap-4 items-center mt-1 text-gray-700 text-sm">
                    <div class="w-20 h-10">
                        <img :src="flag_img" :alt="flag" class="border h-full">
                    </div>
                    <div>
                        Default currency: <b v-text="currency"></b>
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>

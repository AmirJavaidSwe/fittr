<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
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
    address_line1: props.form_data.address_line1,
    address_line2: props.form_data.address_line2,
    city: props.form_data.city,
    state: props.form_data.state,
    legal_country_id: props.form_data.legal_country_id,
    zip_code: props.form_data.zip_code,
});

const show_states = ref(false);
const countryChanged = () => {
    let country = props.countries.find(({ id }) => id == form.legal_country_id);
    show_states.value = country?.has_states ?? false;
};
onMounted(() => {
    countryChanged();
});

const submitForm = () => {
    form.put(route('partner.settings.general-address.update'), {
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
            <!-- Address Line 1 -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="address_line1" value="Address Line 1" />
                <TextInput
                    id="address_line1"
                    v-model="form.address_line1"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.address_line1" class="mt-2" />
            </div>

            <!-- Address Line 2 -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="address_line2" value="Address Line 2" />
                <TextInput
                    id="address_line2"
                    v-model="form.address_line2"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.address_line2" class="mt-2" />
            </div>

            <!-- City -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="city" value="City" />
                <TextInput
                    id="city"
                    v-model="form.city"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.city" class="mt-2" />
            </div>

            <!-- State -->
            <div v-if="show_states" class="col-span-6 sm:col-span-4">
                <InputLabel for="state" value="State" />
                <TextInput
                    id="state"
                    v-model="form.state"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.state" class="mt-2" />
            </div>

            <!-- Zip -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="zip_code" value="ZIP Code" />
                <TextInput
                    id="city"
                    v-model="form.zip_code"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.zip_code" class="mt-2" />
            </div>

            <!-- Country -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="legal_country_id" value="Country" />
                <SelectInput
                    id="legal_country_id"
                    v-model="form.legal_country_id"
                    :options="props.countries"
                    option_value="id"
                    option_text="name"
                    class="mt-1 block w-full"
                    @change="countryChanged"
                >
                </SelectInput>
                <InputError :message="form.errors.legal_country_id" class="mt-2" />
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

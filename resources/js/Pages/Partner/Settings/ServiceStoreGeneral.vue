<script setup>
import { computed, ref, onMounted  } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ServiceStoreMenu from '@/Pages/Partner/Settings/ServiceStoreMenu.vue';

import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    form_data: Object,
});

const form = useForm({
    subdomain: props.form_data.subdomain,
    custom_domain: props.form_data.custom_domain,
});

const submitForm = () => {
    form.put(route('partner.settings.service-store-general'), {
        preserveScroll: true
    });
};
</script>

<template>
    <FormSection @submitted="submitForm">
        <template #description>
            <ServiceStoreMenu />
        </template>

        <template #form>
            <!-- subdomain -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="subdomain" value="Service Store URL" />
                <TextInput
                    id="subdomain"
                    v-model="form.subdomain"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.subdomain" class="mt-2" />
            </div>
            <!-- Custom Domain -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="custom_domain" value="Custom Domain" />
                <TextInput
                    id="custom_domain"
                    v-model="form.custom_domain"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.custom_domain" class="mt-2" />
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

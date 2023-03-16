<script setup>
import { computed, ref, onMounted  } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ServiceStoreMenu from '@/Pages/Partner/Settings/ServiceStoreMenu.vue';

import SectionTitle from '@/Components/SectionTitle.vue';
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
    google_analytics: props.form_data.google_analytics,
    google_gtag: props.form_data.google_gtag,
    google_adsense: props.form_data.google_adsense,
    fb_pixel: props.form_data.fb_pixel,
});

const submitForm = () => {
    form.put(route('partner.settings.service-store-code'), {
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
            <div class="col-span-full">
                <SectionTitle>
                    <template #title>
                        Custom code
                    </template>
                </SectionTitle>
                <div class="text-sm text-gray-600">
                    <p>Add analytics, event tracking code and more.</p>
                </div>
            </div>
            <!-- Google Analytics -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Google Analytics" />
                <div class="text-sm text-gray-600">Google Universal Analytics Tracking ID.</div>
                <TextInput
                    id="logo_url"
                    v-model="form.google_analytics"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g UA-XXXXXXX"
                />
                <InputError :message="form.errors.google_analytics" class="mt-2" />
            </div>

            <!-- Google Tag Manager -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Google Tag Manager" />
                <div class="text-sm text-gray-600">With Google Tag Manager you can add and manage various types of tags (Google Analytics, Google Ads Conversion, Facebook Pixel and many more) with single integration.</div>
                <TextInput
                    id="logo_url"
                    v-model="form.google_gtag"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g GTM-1AXXXXX"
                />
                <InputError :message="form.errors.google_gtag" class="mt-2" />
            </div>

            <!-- Google AdSense - ads -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Google AdSense" />
                <div class="text-sm text-gray-600">Add your publisher ID if you plan showing ads on your website.</div>
                <TextInput
                    id="logo_url"
                    v-model="form.google_adsense"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g ca-pub-1234XXXXXXXXXXXX"
                />
                <InputError :message="form.errors.google_adsense" class="mt-2" />
            </div>

            <!-- Facebook Pixel -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Facebook (Meta) Pixel" />
                <div class="text-sm text-gray-600">Pixel ID.</div>
                <TextInput
                    id="logo_url"
                    v-model="form.fb_pixel"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g 1234XXXXXXXXXXX"
                />
                <InputError :message="form.errors.fb_pixel" class="mt-2" />
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

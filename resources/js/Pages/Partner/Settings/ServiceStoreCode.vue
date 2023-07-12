<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";

import SectionTitleGeneral from "@/Components/SectionTitleGeneral.vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

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
    form.put(route("partner.settings.service-store-code"), {
        preserveScroll: true,
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
                <SectionTitleGeneral>
                    <template #title> <h2 class="text-3xl"><strong>Custom code</strong></h2> </template>
                </SectionTitleGeneral>
                <div class="text-lg text-gray-400">
                    <p>Add analytics, event tracking code and more.</p>
                </div>
            </div>
            <!-- Google Analytics -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Google Analytics" />
                <TextInput
                    id="logo_url"
                    v-model="form.google_analytics"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g UA-XXXXXXX"
                />
                <div class="text-sm text-gray-600 mt-2">
                    Google Universal Analytics Tracking ID.
                </div>
                <InputError
                    :message="form.errors.google_analytics"
                    class="mt-2"
                />
            </div>

            <!-- Google Tag Manager -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Google Tag Manager" />
                <TextInput
                    id="logo_url"
                    v-model="form.google_gtag"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g GTM-1AXXXXX"
                />
                <div class="text-sm text-gray-600 mt-2">
                    With Google Tag Manager you can add and manage various types
                    of tags (Google Analytics, Google Ads Conversion, Facebook
                    Pixel and many more) with single integration.
                </div>
                <InputError :message="form.errors.google_gtag" class="mt-2" />
            </div>

            <!-- Google AdSense - ads -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Google AdSense" />
                
                <TextInput
                    id="logo_url"
                    v-model="form.google_adsense"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g ca-pub-1234XXXXXXXXXXXX"
                />
                <div class="text-sm text-gray-600 mb-3 mt-2">
                    Add your publisher ID if you plan showing ads on your
                    website.
                </div>
                <InputError
                    :message="form.errors.google_adsense"
                    class="mt-2"
                />
            </div>

            <!-- Facebook Pixel -->
            <div class="col-span-6 sm:col-span-4 mt-8">
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

            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>
</template>

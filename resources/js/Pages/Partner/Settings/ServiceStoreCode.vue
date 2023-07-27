<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";
import ServiceStoreVerticalTabs from "@/Pages/Partner/Settings/ServiceStoreVerticalTabs.vue";
import FormSectionVertical from "@/Components/FormSectionVertical.vue";
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
    <ServiceStoreMenu class="lg:hidden" />
    <FormSectionVertical @submitted="submitForm">
        <template #tabsList>
            <ServiceStoreVerticalTabs />
        </template>
        <template #heading>
            <h3 class="text-2xl pt-3 pb-3 font-bold">Custom Code</h3>
        </template>
        <template #form>
            <p>Add analytics, event tracking code and more.</p>
            <div class="col-span-6 sm:col-span-4 mt-6">
                <InputLabel for="meta_title" value="Google Analytics" />

                <TextInput id="logo_url" v-model="form.google_analytics" type="text" class="mt-1 block w-full"
                    placeholder="e.g UA-XXXXXXX" />
                <InputError :message="form.errors.google_analytics" class="mt-2" />
                <div class="text-sm text-gray-600 pt-2">
                    Google Universal Analytics Tracking ID.
                </div>
            </div>
            <!-- Google Tag Manager -->
            <div class="col-span-6 sm:col-span-4 mt-6">
                <InputLabel for="meta_title" value="Google Tag Manager" />
                <TextInput id="logo_url" v-model="form.google_gtag" type="text" class="mt-1 block w-full"
                    placeholder="e.g GTM-1AXXXXX" />
                <InputError :message="form.errors.google_gtag" class="mt-2" />
                <div class="text-sm text-gray-600 pt-3">
                    With Google Tag Manager you can add and manage various types
                    of tags (Google Analytics, Google Ads Conversion, Facebook
                    Pixel and many more) with single integration.
                </div>
            </div>
            <!-- Google AdSense - ads -->
            <div class="col-span-6 sm:col-span-4 mt-5">
                <InputLabel for="meta_title" value="Google AdSense" />

                <TextInput id="logo_url" v-model="form.google_adsense" type="text" class="mt-1 block w-full"
                    placeholder="e.g ca-pub-1234XXXXXXXXXXXX" />
                <InputError :message="form.errors.google_adsense" class="mt-2" />
                <div class="text-sm text-gray-600">
                    Add your publisher ID if you plan showing ads on your
                    website.
                </div>

            </div>
            <!-- Facebook Pixel -->
            <div class="col-span-6 sm:col-span-4 mt-5">
                <InputLabel for="meta_title" value="Facebook (Meta) Pixel" />
                <TextInput id="logo_url" v-model="form.fb_pixel" type="text" class="mt-1 block w-full"
                    placeholder="e.g 1234XXXXXXXXXXX" />
                <InputError :message="form.errors.fb_pixel" class="mt-2" />
                <div class="text-sm text-gray-600 pt-2">Pixel ID.</div>
            </div>
        </template>
        <template #actions>
            <div class="flex mt-5">
                <ActionMessage :on="form.recentlySuccessful" class="font-semibold mr-3 mt-3">
                    Saved.
                </ActionMessage>
                <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Save
                </ButtonLink>
            </div>
        </template>
    </FormSectionVertical>
</template>

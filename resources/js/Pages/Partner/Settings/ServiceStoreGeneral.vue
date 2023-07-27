<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";
import ServiceStoreVerticalTabs from "@/Pages/Partner/Settings/ServiceStoreVerticalTabs.vue";

import FormSection from "@/Components/FormSection.vue";
import FormSectionVertical from "@/Components/FormSectionVertical.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ImageUploadFiles from "@/Pages/Partner/Settings/ImageUploadFiles.vue";
import LinkIcon from "@/Icons/LinkIcon.vue";
import FacebookIcon from "@/Icons/FacebookIcon.vue";
import InstagramIcon from "@/Icons/InstagramIcon.vue";
import TwitterIcon from "@/Icons/TwitterIcon.vue";
import YoutubeIcon from "@/Icons/YoutubeIcon.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    form_data: Object,
});

const form = useForm({
    subdomain: props.form_data.subdomain,
    custom_domain: props.form_data.custom_domain,
});

const submitForm = () => {
    form.put(route("partner.settings.service-store-general"), {
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
            <h3 class="text-2xl pt-3 pb-3 font-bold">General</h3>
        </template>
        <template #form>
            <div class="col-span-12 sm:col-span-12 mt-4">
                <InputLabel for="subdomain" value="Service Store URL" />
                <div class="relative flex items-center">
                    <span class="w-28 rounded-s-lg border md:h-10 pt-0 md:pt-2 mt-1 flex gap-2 justify-center">
                        <span class="">
                            <LinkIcon />
                        </span>
                        <span>https://</span>
                    </span>
                    <TextInput id="subdomain" v-model="form.subdomain" type="text"
                        class="mt-1 block w-full start-radius-none" />
                    <InputError :message="form.errors.subdomain" class="mt-2" />
                    <InputError :message="form.errors.unique" class="mt-2" />
                </div>
                <p class="mt-2" v-if="form.subdomain">
                    Your service store URL will be:
                    <b>https://{{ form.subdomain }}.{{
                        $page.props.app_domain
                    }}</b>
                </p>
            </div>
            <!-- Custom Domain -->
            <div class="col-span-12 sm:col-span-12 mt-4">
                <InputLabel for="custom_domain" value="Custom Domain" />
                <TextInput id="custom_domain" v-model="form.custom_domain" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.custom_domain" class="mt-2" />
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

<script setup>
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";
import ServiceStoreVerticalTabs from "@/Pages/Partner/Settings/ServiceStoreVerticalTabs.vue";
import Dropzone from "@/Components/Dropzone.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import FormSectionVertical from "@/Components/FormSectionVertical.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import LinkIcon from "@/Icons/LinkIcon.vue";
import FacebookIcon from "@/Icons/FacebookIcon.vue";
import InstagramIcon from "@/Icons/InstagramIcon.vue";
import TwitterIcon from "@/Icons/TwitterIcon.vue";
import YoutubeIcon from "@/Icons/YoutubeIcon.vue";
import Switcher from "@/Components/Switcher.vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faArrowsDownToLine,
    faArrowsUpToLine,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    business_seetings: Object,
    form_data: Object,
});

const form = useForm({
    _method: "PUT", //use spoofing for multipart/form-data support
    logo: null, //file
    logo_url: props?.form_data?.logo_url,
    favicon: null, //file
    show_address: props.form_data.show_address,
    show_phone: props.form_data.show_phone,
    show_email: props.form_data.show_email,
    link_facebook: props.form_data.link_facebook,
    link_instagram: props.form_data.link_instagram,
    link_twitter: props.form_data.link_twitter,
    link_youtube: props.form_data.link_youtube,
});

const section_header = ref(null);
const section_footer = ref(null);
const scrollToHeader = () => {
    section_header.value?.scrollIntoView({ behavior: "smooth" });
};
const scrollToFooter = () => {
    section_footer.value?.scrollIntoView({ behavior: "smooth" });
};

const logo_img_url = computed(() => {
    return props.form_data.logo ? usePage().props.asset_url + props.form_data.logo : null;
});


const favicon_img_url = computed(() => {
    return props.form_data.favicon ? usePage().props.asset_url + props.form_data.favicon : null;
});


const submitForm = () => {
    // let null pass for images when deletion requested || provide File object (new file) or undefined (no change)
    // when form sent as json and not multipart/form-data, inertia will exclude props with undefined values
    form.transform((data) => {
        if (data.logo === null) delete data.logo;
        else if (data.logo === undefined) data.logo = null
        if (data.favicon === null) delete data.favicon;
        else if (data.favicon === undefined) data.favicon = null
        return data;
    }).post(route("partner.settings.service-store-header"), {
        preserveScroll: true,
    });
};

const removeFile = ($event) => {
    form[$event.instance_name] = undefined
}
</script>

<template>
    <ServiceStoreMenu class="lg:hidden" />
    <FormSectionVertical @submitted="submitForm">
        <template #tabsList>
            <ServiceStoreVerticalTabs />
        </template>
        <template #heading>
            <h3 class="text-2xl pt-3 pb-3 font-bold">Header and Footer</h3>
        </template>
        <template #form>

            <div ref="section_header" class="col-span-full">
                <SectionTitle>
                    <template #title> Header Settings </template>
                    <template #aside>
                        <div @click="scrollToFooter" class="cursor-pointer">
                            <font-awesome-icon :icon="faArrowsDownToLine" />
                            Footer Settings
                        </div>
                    </template>
                </SectionTitle>
            </div>

            <div class="col-span-12 sm:col-span-12 mt-4">
                <div class="uploadfiles">
                    <InputLabel for="header-image" value="Business Logo" />
                    <Dropzone id="header-image" v-model="form.logo" :uploaded_file="logo_img_url ? logo_img_url : ''"
                        :accept="['.jpg', '.jpeg', '.png', '.webp', '.svg']" max_width="200" max_height="200"
                        :buttonText="'Select new business logo'" :instance_name="'logo'" @remove_file="removeFile" />
                    <InputError :message="form.errors.logo" class="mt-2" />
                </div>
            </div>
            <div class="col-span-12 sm:col-span-12 mt-4">
                <InputLabel for="logo_url" value="Logo URL" />
                <div class="text-sm text-gray-600">
                    <p>
                        Add a redirect to home or any other page when clients
                        click on the logo.
                    </p>
                    <p>
                        NOTE : By default, a click on the logo will redirect to
                        the Service Store homepage.
                    </p>
                </div>
                <div class="relative flex items-center">
                    <span class="w-28 rounded-s-lg border md:h-10 pt-0 md:pt-2 mt-1 flex gap-2 justify-center">
                        <span class="">
                            <LinkIcon />
                        </span>
                        <span>https://</span>
                    </span>
                    <TextInput id="logo_url" v-model="form.logo_url" type="text"
                        class="mt-1 block w-full start-radius-none" placeholder="https://www.example.com/xyz" />
                    <InputError :message="form.errors.logo_url" class="mt-2" />
                </div>
            </div>
            <div class="col-span-12 sm:col-span-12 mt-4">
                <div class="uploadfiles">
                    <InputLabel for="favicon_url" value="Favicon" />
                    <Dropzone id="favicon_url" v-model="form.favicon"
                    :uploaded_file="favicon_img_url ? favicon_img_url : ''" :img_used_for="'business_favicon'"
                    :accept="['.ico', '.svg', '.png']" max_width="200" max_height="200" :buttonText="'Select new favicon'"
                    :instance_name="'favicon'" @remove_file="removeFile" />
                    <InputError :message="form.errors.favicon" class="mt-2" />
                </div>
            </div>

            <div ref="section_footer" class="col-span-full mt-8">
                <SectionTitle>
                    <template #title> Footer Settings </template>
                    <template #aside>
                        <div @click="scrollToHeader" class="cursor-pointer">
                            <font-awesome-icon :icon="faArrowsUpToLine" />
                            Header Settings
                        </div>
                    </template>
                </SectionTitle>
                <div class="font-lg font-bold">Social Accounts</div>
                <div class="text-sm border-l-[3px] border-primary-500">
                    <span class="ml-3 text-gray-500">
                        Please make sure that all links start with https://
                    </span>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-1">
                        <InputLabel for="link_facebook" value="Facebook" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-[0.6rem] md:top-[0.9rem] pl-2 pr-4">
                            <FacebookIcon />
                        </span>
                        <TextInput id="link_facebook" v-model="form.link_facebook" type="text"
                            class="mt-1 block w-full pl-10 md:pl-12" placeholder="https://www.facebook.com/xyz" />
                        <InputError :message="form.errors.link_facebook" class="mt-2" />
                    </div>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-1">
                        <InputLabel for="link_instagram" value="Instagram" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-[0.8rem] md:top-[1rem] pl-2 pr-4">
                            <InstagramIcon />
                        </span>
                        <TextInput id="link_instagram" placeholder="https://www.instagram.com/xyz" v-model="form.link_instagram" type="text"
                        class="mt-1 block w-full pl-10 md:pl-12" />
                        <InputError :message="form.errors.link_instagram" class="mt-2" />

                    </div>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-1">
                        <InputLabel for="link_twitter" value="Twitter" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-[0.6rem] md:top-[0.8rem] pl-2 pr-4">
                            <TwitterIcon />
                        </span>
                        <TextInput id="link_twitter" placeholder="https://twitter.com/xyz" v-model="form.link_twitter" type="text"
                            class="mt-1 block w-full pl-10 md:pl-12" />
                            <InputError :message="form.errors.link_twitter" class="mt-2" />
                    </div>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-1">
                        <InputLabel for="link_youtube" value="Youtube" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-[0.6rem] md:top-[0.8rem] pl-2 pr-4">
                            <YoutubeIcon />
                        </span>
                        <TextInput id="link_youtube" v-model="form.link_youtube" type="text"
                            class="mt-1 block w-full pl-10 md:pl-12" placeholder="Link" />
                        <InputError :message="form.errors.link_youtube" class="mt-2" />
                    </div>
                </div>
                <span class="block text-gray-400 block mb-5">Footer Options</span>
                <div class="mt-4 mb-4">
                    <Switcher class="w-full flex justify-between" v-model="form.show_address"
                        title="Show address in Service Store" description="" />
                </div>

                <div class="mt-4 mb-4">
                    <Switcher class="w-full flex justify-between" v-model="form.show_phone"
                        title="Show Phone Number in Service Store" description="" />
                </div>

                <div class="mt-4 mb-4">
                    <Switcher class="w-full flex justify-between" v-model="form.show_email"
                        title="Show Email in Service Store" description="" />
                </div>
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

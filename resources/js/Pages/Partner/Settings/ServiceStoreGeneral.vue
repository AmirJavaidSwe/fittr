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
    <ServiceStoreMenu class="md:hidden" />
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
                    <span class="w-28 rounded-s-lg border md:h-10 pt-2 mt-1 flex gap-2 justify-center">
                        <span class="">
                            <LinkIcon />
                        </span>
                        <span>https:</span>
                    </span>
                    <TextInput id="subdomain" v-model="form.subdomain" type="text"
                        class="mt-1 block w-full startradiusnone" />
                    <InputError :message="form.errors.subdomain" class="mt-2" />
                    <InputError :message="form.errors.unique" class="mt-2" />
                </div>
                <p class="mt-2" v-if="form.subdomain">
                    Your service store URL will be:
                    <b>https://{{ form.subdomain }}.{{
                        $page.props.app_domain
                    }}</b>
                </p>
                <InputError :message="form.errors.subdomain" class="mt-2" />
                <InputError :message="form.errors.unique" class="mt-2" />

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

    
    <!--header and footer-->
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col md:gap-5">
            <div class="row-span-2 hidden md:block col-span-1 p-5">
                <ul class="vertical-tabs">
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">General</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition active" href="#">Header &amp; Footer</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">SEO</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">Custom code</a>
                    </li>
                </ul>
            </div>
            <div class="row-span-7 col-span-7 border-gray-200 p-5">
                <h3 class="text-xl pt-3 pb-3"><strong>Header and Footer</strong></h3>
                <span class="block text-gray-400 mt-5">Header settings</span>
                <div class="uploadfiles">
                    <ImageUploadFiles />
                </div>
                <div class="col-span-12 sm:col-span-12 mt-4">
                    <InputLabel for="subdomain" value="Service Store URL" />
                    <div class="relative flex items-center">
                        <span class="w-28 rounded-s-lg border md:h-10 pt-2 mt-1 flex gap-2 justify-center">
                            <span class="">
                                <LinkIcon />
                            </span>
                            <span>https:</span>
                        </span>
                        <TextInput id="subdomain" v-model="form.subdomain" type="text"
                            class="mt-1 block w-full startradiusnone" />

                        <InputError :message="form.errors.subdomain" class="mt-2" />
                        <InputError :message="form.errors.unique" class="mt-2" />
                    </div>
                    <p class="text-gray-400 pt-2">By default, a click on the logo will redirect to the Service Store
                        homepage.</p>
                </div>
                <!-- Custom Domain -->
                <div class="col-span-12 sm:col-span-12 mt-4">
                    <InputLabel for="custom_domain" value="Custom Domain" />
                    <TextInput id="custom_domain" v-model="form.custom_domain" type="text" class="mt-1 block w-full" />
                    <InputError :message="form.errors.custom_domain" class="mt-2" />
                </div>
                <div class="uploadfiles">
                    <ImageUploadFiles />
                </div>
                <span class="block text-gray-400 block mb-5">Footer settings</span>
                <strong>Social Accounts</strong>
                <p class="mt-2 mb-8">Please make sure that all links start https://</p>

                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-2">
                        <InputLabel for="custom_domain" value="Facebook" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-4 pl-5">
                            <FacebookIcon />
                        </span>
                        <TextInput id="custom_domain" v-model="form.custom_domain" type="text"
                            class="mt-1 block w-full pl-14" placeholder="Link" />
                    </div>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-2">
                        <InputLabel for="custom_domain" value="Instagram" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-4 pl-5">
                            <InstagramIcon />
                        </span>
                        <TextInput id="custom_domain" v-model="form.custom_domain" type="text"
                            class="mt-1 block w-full pl-14" placeholder="Link" />
                    </div>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-2">
                        <InputLabel for="custom_domain" value="Twitter" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-4 pl-5">
                            <TwitterIcon />
                        </span>
                        <TextInput id="custom_domain" v-model="form.custom_domain" type="text"
                            class="mt-1 block w-full pl-14" placeholder="Link" />
                    </div>
                </div>
                <div class="flex flex-row flex-wrap mt-8 mb-8">
                    <div class="mb-2">
                        <InputLabel for="custom_domain" value="Youtube" />
                    </div>
                    <div class="relative w-full">
                        <span class="absolute top-4 pl-5">
                            <YoutubeIcon />
                        </span>
                        <TextInput id="custom_domain" v-model="form.custom_domain" type="text"
                            class="mt-1 block w-full pl-14" placeholder="Link" />
                    </div>
                </div>
                <span class="block text-gray-400 block mb-5">Footer Options</span>
                <div class="mt-4 mb-4">
                    <Switcher class="w-full flex justify-between" v-model="form.is_off_peak"
                        title="Show address in Service Store" description="" />
                </div>

                <div class="mt-4 mb-4">
                    <Switcher class="w-full flex justify-between" v-model="form.is_off_peak"
                        title="Show Phone Number in Service Store" description="" />
                </div>

                <div class="mt-4 mb-4">
                    <Switcher class="w-full flex justify-between" v-model="form.is_off_peak"
                        title="Show Email  in Service Store" description="" />
                </div>

                <div class="flex mt-8">
                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Save
                    </ButtonLink>
                </div>
            </div>
        </div>
    </div>
    <!-- start seo-->
    <!--header and footer-->
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col md:gap-5">
            <div class="row-span-2 hidden md:block col-span-1 p-5">
                <ul class="vertical-tabs">
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">General</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition " href="#">Header &amp; Footer</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition active" href="#">SEO</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">Custom code</a>
                    </li>
                </ul>
            </div>
            <div class="row-span-7 col-span-7 border-gray-200 p-5">
                <h3 class="text-xl pt-3 pb-2"><strong>SEO Settings</strong></h3>
                <p>Specify your Service Store's title & description to be displayed in search engine results pages (SERPs).
                </p>
                <div
                    class="bg-mainBg w-full  border border-gray-200 flex flex-col gap-4 p-3 lg:p-4 rounded-lg shadow-md sm:max-w-full my-4">
                    <div
                        class="pt-2 pb-2 border-b-2 border-slate-300 text-xl 2xl:text-3xl font-bold tracking-tight text-gray-900">
                        Google search results preview </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-7 h-7 mr-2">
                                <div
                                    class="bg-secondary-500 p-1 text-white rounded-full flex items-center justify-center text-center uppercase font-semibold w-8 h-8 2xl:w-12 2xl:h-12 text-md lg:text-md 2xl:text-lg">
                                    DP</div>
                            </span>
                            <div>
                                <span class="text-sm"><strong>Biz name</strong></span>
                                <div class="text-xs"> https://your-site.fittr.tech </div>
                            </div>
                        </div>
                        <div class="font-bold text-xl text-blue-800">This is an example of a title tag</div>
                        <div class="text-sm">Here is an example of what snippet looks like in Google's SERPs. The content
                            that appears here is usually taken from Meta Description tag if relevant.</div>
                    </div>
                    <!--v-if-->
                </div>
                <div class="mt-8 mb-4">
                    <InputLabel for="meta_title" value="Meta Title" />
                    <TextInput id="logo_url" v-model="form.meta_title" type="text" class="mt-1 block w-full"
                        placeholder="Title of your page. Optimal length is 55-60. Max 255." />
                    <div v-if="form.meta_title" class="text-sm">
                        Chars: {{ form.meta_title.length }}
                    </div>
                    <InputError :message="form.errors.meta_title" class="mt-2" />
                </div>
                <!-- Meta Description -->
                <div class="mt-8 mb-4">
                    <InputLabel for="meta_description" value="Meta Description" />
                    <TextArea id="meta_description" v-model="form.meta_description" type="text"
                        class="mt-1 block w-full rounded"
                        placeholder="Describe your page here. Optimal length is 100-160 characters. Max 255." />
                    <div v-if="form.meta_description" class="text-sm">
                        Chars: {{ form.meta_description.length }}
                    </div>
                    <InputError :message="form.errors.meta_description" class="mt-2" />
                </div>
                <div class="flex">
                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Saves
                    </ButtonLink>
                </div>
            </div>
        </div>
    </div>
    <!--custom code-->
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col md:gap-5">
            <div class="row-span-2 hidden md:block col-span-1 p-5">
                <ul class="vertical-tabs">
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">General</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition " href="#">Header &amp; Footer</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition" href="#">SEO</a>
                    </li>
                    <li>
                        <a class="p-2 block hover:underline transition active" href="#">Custom code</a>
                    </li>
                </ul>
            </div>
            <div class="row-span-7 col-span-7 border-gray-200 p-5">
                <h3 class="text-xl pt-3 pb-2"><strong>Custom Code</strong></h3>
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
                    <div class="text-sm text-gray-600 pt-3">
                        With Google Tag Manager you can add and manage various types
                        of tags (Google Analytics, Google Ads Conversion, Facebook
                        Pixel and many more) with single integration.
                    </div>
                    <InputError :message="form.errors.google_gtag" class="mt-2" />
                </div>
                <!-- Google AdSense - ads -->
                <div class="col-span-6 sm:col-span-4 mt-5">
                    <InputLabel for="meta_title" value="Google AdSense" />

                    <TextInput id="logo_url" v-model="form.google_adsense" type="text" class="mt-1 block w-full"
                        placeholder="e.g ca-pub-1234XXXXXXXXXXXX" />
                    <div class="text-sm text-gray-600">
                        Add your publisher ID if you plan showing ads on your
                        website.
                    </div>
                    <InputError :message="form.errors.google_adsense" class="mt-2" />

                </div>
                <!-- Facebook Pixel -->
                <div class="col-span-6 sm:col-span-4 mt-5">
                    <InputLabel for="meta_title" value="Facebook (Meta) Pixel" />
                    <TextInput id="logo_url" v-model="form.fb_pixel" type="text" class="mt-1 block w-full"
                        placeholder="e.g 1234XXXXXXXXXXX" />
                    <div class="text-sm text-gray-600 pt-2">Pixel ID.</div>
                    <InputError :message="form.errors.fb_pixel" class="mt-2" />
                </div>

                <div class="flex mt-8">
                    <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Save
                    </ButtonLink>
                </div>


            </div>

        </div>
    </div>
</template>

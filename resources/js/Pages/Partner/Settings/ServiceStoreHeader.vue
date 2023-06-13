<script setup>
import { computed, ref, onMounted } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";

import SectionTitle from "@/Components/SectionTitle.vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

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
    logo: props.form_data.logo, //file
    logo_url: props.form_data.logo_url,
    favicon: props.form_data.favicon, //file
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

const logoPreview = ref(null);
const logoInput = ref(null);
const logoDelete = ref(false);
const faviconPreview = ref(null);
const faviconInput = ref(null);
const faviconDelete = ref(false);
const updateLogoPreview = () => {
    const img = logoInput.value.files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
        logoPreview.value = e.target.result;
        logoDelete.value = false;
    };
    reader.readAsDataURL(img);
};

const selectNewLogo = () => {
    logoInput.value.click();
};

const deleteLogo = () => {
    logoDelete.value = true;
};

const logo_img_url = computed(() => {
    return form.logo ? usePage().props.asset_url + form.logo : null;
});

const show_logo = computed(() => {
    return (form.logo || logoPreview.value) && !logoDelete.value;
});

const updateFaviconPreview = () => {
    const reader = new FileReader();
    reader.onload = (e) => {
        faviconPreview.value = e.target.result;
        faviconDelete.value = false;
    };
    reader.readAsDataURL(faviconInput.value.files[0]);
};

const selectNewFavicon = () => {
    faviconInput.value.click();
};

const deleteFavicon = () => {
    faviconDelete.value = true;
};

const favicon_img_url = computed(() => {
    return form.favicon ? usePage().props.asset_url + form.favicon : null;
});

const show_favicon = computed(() => {
    return (form.favicon || faviconPreview.value) && !faviconDelete.value;
});

const submitForm = () => {
    //let null pass for images when deletion requested || provide File object (new file) or undefined (no change)
    //when form sent as json and not multipart/form-data, inertia will exclude props with undefined values
    form.logo = logoDelete.value ? null : logoInput.value.files[0];
    form.favicon = faviconDelete.value ? null : faviconInput.value.files[0];
    form.transform((data) => {
        if (data.logo === undefined) {
            delete data.logo;
        }
        if (data.favicon === undefined) {
            delete data.favicon;
        }
        return data;
    }).post(route("partner.settings.service-store-header"), {
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
            <!-- HEADER -->
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
            <!-- Logo -->
            <div class="col-span-6 sm:col-span-4">
                <input
                    ref="logoInput"
                    type="file"
                    class="hidden"
                    @change="updateLogoPreview"
                />

                <InputLabel for="logo" value="Business Logo" />
                <div class="text-sm text-gray-600">
                    <p>Upload a PNG, JPG or SVG logo image.</p>
                </div>

                <div v-if="show_logo">
                    <!-- Current Logo -->
                    <div v-show="!logoPreview && form.logo" class="mt-2">
                        <img :src="logo_img_url" class="h-32" />
                    </div>

                    <!-- New Logo Preview -->
                    <div v-show="logoPreview" class="mt-2">
                        <img :src="logoPreview" class="h-32" />
                    </div>
                </div>

                <ButtonLink
                    styling="default"
                    size="small"
                    class="mt-2 mr-2"
                    type="button"
                    @click.prevent="selectNewLogo"
                >
                    Select new logo
                </ButtonLink>

                <ButtonLink
                    styling="default"
                    size="small"
                    v-if="!logoDelete && form.logo"
                    type="button"
                    class="mt-2"
                    @click="deleteLogo"
                >
                    Delete logo
                </ButtonLink>
            </div>
            <!-- Logo URL -->
            <div class="col-span-6 sm:col-span-4">
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
                <TextInput
                    id="logo_url"
                    v-model="form.logo_url"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.logo_url" class="mt-2" />
            </div>
            <!-- favicon -->
            <div class="col-span-6 sm:col-span-4">
                <input
                    ref="faviconInput"
                    type="file"
                    class="hidden"
                    @change="updateFaviconPreview"
                />

                <InputLabel for="favicon" value="Favicon" />
                <div class="text-sm text-gray-600">
                    <p>Upload a PNG, or JPG favicon image.</p>
                </div>

                <div v-if="show_favicon">
                    <!-- Current favicon -->
                    <div v-show="!faviconPreview && form.favicon" class="mt-2">
                        <img
                            :src="favicon_img_url"
                            class="bg-gray-900 h-12 p-2"
                        />
                    </div>

                    <!-- New favicon -->
                    <div v-show="faviconPreview" class="mt-2">
                        <img
                            :src="faviconPreview"
                            class="bg-gray-900 h-12 p-2"
                        />
                    </div>
                </div>

                <ButtonLink
                    styling="default"
                    size="default"
                    class="mt-2 mr-2"
                    type="button"
                    @click.prevent="selectNewFavicon"
                >
                    Select new icon
                </ButtonLink>

                <ButtonLink
                    styling="default"
                    size="default"
                    v-if="!faviconDelete && form.favicon"
                    type="button"
                    class="mt-2"
                    @click="deleteFavicon"
                >
                    Delete favicon
                </ButtonLink>
            </div>

            <!-- FOOTER -->
            <div ref="section_footer" class="col-span-full">
                <SectionTitle>
                    <template #title> Footer Settings </template>
                    <template #aside>
                        <div @click="scrollToHeader" class="cursor-pointer">
                            <font-awesome-icon :icon="faArrowsUpToLine" />
                            Header Settings
                        </div>
                    </template>
                </SectionTitle>
                <div class="font-medium">Social Accounts</div>
                <div class="text-sm">
                    Please make sure that all links start with https://
                </div>
            </div>

            <!-- Facebook -->
            <div class="col-span-6 sm:col-span-4">
                <div class="flex gap-2 items-center">
                    <InputLabel
                        class="w-36"
                        for="link_facebook"
                        value="Facebook"
                    />
                    <TextInput
                        id="link_facebook"
                        v-model="form.link_facebook"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Paste URL here"
                    />
                </div>
                <InputError :message="form.errors.link_facebook" class="mt-2" />
            </div>
            <!-- Instagram -->
            <div class="col-span-6 sm:col-span-4">
                <div class="flex gap-2 items-center">
                    <InputLabel
                        class="w-36"
                        for="link_instagram"
                        value="Instagram"
                    />
                    <TextInput
                        id="link_instagram"
                        v-model="form.link_instagram"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Paste URL here"
                    />
                </div>
                <InputError
                    :message="form.errors.link_instagram"
                    class="mt-2"
                />
            </div>
            <!-- Twitter -->
            <div class="col-span-6 sm:col-span-4">
                <div class="flex gap-2 items-center">
                    <InputLabel
                        class="w-36"
                        for="link_twitter"
                        value="Twitter"
                    />
                    <TextInput
                        id="link_twitter"
                        v-model="form.link_twitter"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Paste URL here"
                    />
                </div>
                <InputError :message="form.errors.link_twitter" class="mt-2" />
            </div>
            <!-- YouTube -->
            <div class="col-span-6 sm:col-span-4">
                <div class="flex gap-2 items-center">
                    <InputLabel
                        class="w-36"
                        for="link_youtube"
                        value="YouTube"
                    />
                    <TextInput
                        id="link_twitter"
                        v-model="form.link_youtube"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Paste URL here"
                    />
                </div>
                <InputError :message="form.errors.link_youtube" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4 space-y-4">
                <div class="font-medium">Footer options</div>

                <label class="flex items-center">
                    <span
                        class="mr-4 w-6 text-sm text-gray-700"
                        v-text="form.show_address ? 'On' : 'Off'"
                    ></span>
                    <Checkbox
                        v-model:checked="form.show_address"
                        :value="form.show_address ? '1' : '0'"
                    />
                    <span class="ml-4 text-sm text-gray-700"
                        >Show Address in Service Store</span
                    >
                </label>
                <InputError :message="form.errors.show_address" class="mt-2" />

                <label class="flex items-center">
                    <span
                        class="mr-4 w-6 text-sm text-gray-700"
                        v-text="form.show_phone ? 'On' : 'Off'"
                    ></span>
                    <Checkbox
                        v-model:checked="form.show_phone"
                        :value="form.show_phone ? '1' : '0'"
                    />
                    <span class="ml-4 text-sm text-gray-700"
                        >Show Phone Number in Service Store</span
                    >
                </label>
                <InputError :message="form.errors.show_phone" class="mt-2" />

                <label class="flex items-center">
                    <span
                        class="mr-4 w-6 text-sm text-gray-700"
                        v-text="form.show_email ? 'On' : 'Off'"
                    ></span>
                    <Checkbox
                        v-model:checked="form.show_email"
                        :value="form.show_email ? '1' : '0'"
                    />
                    <span class="ml-4 text-sm text-gray-700"
                        >Show Email in Service Store</span
                    >
                </label>
                <InputError :message="form.errors.show_email" class="mt-2" />
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

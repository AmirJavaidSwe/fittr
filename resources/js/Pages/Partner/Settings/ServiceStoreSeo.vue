<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";

import SectionTitle from "@/Components/SectionTitle.vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import WarningButton from "@/Components/WarningButton.vue";
import CardBasic from "@/Components/CardBasic.vue";

const props = defineProps({
    form_data: Object,
});

const form = useForm({
    meta_title: props.form_data.meta_title,
    meta_description: props.form_data.meta_description,
});

const meta_title = computed(() => {
    let over = form.meta_title?.length > 60;
    return form.meta_title
        ? over
            ? form.meta_title.substring(0, 60) + "..."
            : form.meta_title
        : "This is an example of a title tag";
});
const meta_description = computed(() => {
    let over = form.meta_description?.length > 160;
    return form.meta_description
        ? over
            ? form.meta_description.substring(0, 160) + "..."
            : form.meta_description
        : "Here is an example of what snippet looks like in Google's SERPs. The content that appears here is usually taken from Meta Description tag if relevant.";
});

const submitForm = () => {
    form.put(route("partner.settings.service-store-seo"), {
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
            <div class="col-span-full">
                <SectionTitle>
                    <template #title> SEO Settings </template>
                </SectionTitle>
                <div class="text-sm text-gray-600">
                    <p>
                        Specify your Service Store's title & description to be
                        displayed in search engine results pages (SERPs).
                    </p>
                </div>

                <!-- Google search results preview -->
                <CardBasic class="my-4">
                    <template #header> Google search results preview </template>

                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-7 h-7">
                                <img
                                    src="https://ui-avatars.com/api/?background=0D8ABC&color=fff"
                                    alt="favicon"
                                />
                            </span>
                            <div>
                                <span class="text-sm">Biz name</span>
                                <div class="text-xs">
                                    https://your-site.fittr.tech
                                </div>
                            </div>
                        </div>
                        <div class="font-bold text-xl text-blue-800">
                            {{ meta_title }}
                        </div>
                        <div class="text-sm">{{ meta_description }}</div>
                    </div>
                </CardBasic>
            </div>
            <!-- Meta Title -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_title" value="Meta Title" />
                <TextInput
                    id="logo_url"
                    v-model="form.meta_title"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Title of your page. Optimal length is 55-60. Max 255."
                />
                <div v-if="form.meta_title" class="text-sm">
                    Chars: {{ form.meta_title.length }}
                </div>
                <InputError :message="form.errors.meta_title" class="mt-2" />
            </div>
            <!-- Meta Description -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="meta_description" value="Meta Description" />
                <TextArea
                    id="meta_description"
                    v-model="form.meta_description"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Describe your page here. Optimal length is 100-160 characters. Max 255."
                />
                <div v-if="form.meta_description" class="text-sm">
                    Chars: {{ form.meta_description.length }}
                </div>
                <InputError
                    :message="form.errors.meta_description"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <WarningButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </WarningButton>
        </template>
    </FormSection>
</template>

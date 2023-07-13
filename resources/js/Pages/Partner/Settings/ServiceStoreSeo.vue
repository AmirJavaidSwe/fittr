<script setup>
import { computed, ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";
import ServiceStoreVerticalTabs from "@/Pages/Partner/Settings/ServiceStoreVerticalTabs.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import CardBasic from "@/Components/CardBasic.vue";
import FormSectionVertical from "@/Components/FormSectionVertical.vue";

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
    <ServiceStoreMenu class="lg:hidden" />
    <FormSectionVertical @submitted="submitForm">
        <template #tabsList>
            <ServiceStoreVerticalTabs />
        </template>
        <template #heading>
            <h3 class="text-2xl pt-3 pb-3 font-bold">SEO Settings</h3>
        </template>
        <template #form>
            <p>Specify your Service Store's title & description to be displayed in search engine results pages (SERPs).</p>
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
                    <div class="font-bold text-xl text-blue-800">{{ meta_title }}</div>
                    <div class="text-sm">
                        <div class="border-l-[3px] border-primary-500 mr-3 pl-3">
                            <span class="block">
                                {{ meta_description }}
                            </span>
                        </div>
                    </div>
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
                    class="mt-1 block w-full rounded focus:border-primary-500 shadow-none focus:shadow-none ring-0 focus:outline-none focus:ring-0"
                    placeholder="Describe your page here. Optimal length is 100-160 characters. Max 255." />
                <div v-if="form.meta_description" class="text-sm">
                    Chars: {{ form.meta_description.length }}
                </div>
                <InputError :message="form.errors.meta_description" class="mt-2" />
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

<script setup>
import { useForm } from "@inertiajs/vue3";
import ServiceStoreMenu from "@/Pages/Partner/Settings/ServiceStoreMenu.vue";

import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextArea from "@/Components/TextArea.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    default_waiver: String,
    form_data: Object,
});

const form = useForm({
    waiver_text: props.form_data.waiver_text,
    enforce_waiver: props.form_data.enforce_waiver,
});

const resetToDefault = () => {
    form.waiver_text = props.default_waiver;
};

const submitForm = () => {
    form.put(route("partner.settings.service-store-waivers"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="submitForm">
        <template #form>
            <!-- Waiver Content -->
            <div class="mb-12">
                Customize your waiver that comes up when clients sign up on your
                service store, or when they are making a booking.
            </div>
            <div class="col-span-6">
                <div class="flex justify-between">
                    <InputLabel for="waiver_text" value="Waiver Content" />
                    <div
                        @click="resetToDefault"
                        class="cursor-pointer text-red-500"
                    >
                        Set to default
                    </div>
                </div>
                <TextArea
                    id="waiver_text"
                    v-model="form.waiver_text"
                    :rows="10"
                    class="mt-1 block w-full md:w-2/3 t-1 block rounded focus:border-primary-500 shadow-none focus:shadow-none ring-0 focus:outline-none focus:ring-0"
                    placeholder="Place your waiver content here. HTML tags not allowed."
                />
                <InputError :message="form.errors.waiver_text" class="mt-2" />

                <div class="bg-mainBg w-full md:w-2/3 mt-4 p-5 rounded mt-6">
                    <Switcher v-model="form.enforce_waiver" title="Enforce waiver before checkout"
                        description="Enabling this option will show the waiver and require acceptance on booking page." description-left-border />
                    <InputError :message="form.errors.show_timezone" class="mt-2" />
                </div>

                <!-- <label class="flex items-center">
                    <span
                        class="mr-4 w-6 text-sm text-gray-700"
                        v-text="form.enforce_waiver ? 'On' : 'Off'"
                    ></span>
                    <Checkbox
                        v-model:checked="form.enforce_waiver"
                        :value="form.enforce_waiver ? '1' : '0'"
                    />
                    <span class="ml-4 text-sm text-gray-700"
                        >Enforce waiver before checkout</span
                    >
                </label>
                <div class="text-sm">
                    Enabling this option will show the waiver and require
                    acceptance on booking page.
                </div> -->
                <InputError
                    :message="form.errors.enforce_waiver"
                    class="mt-2"
                />
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

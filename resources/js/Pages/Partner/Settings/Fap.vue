<script setup>
import { useForm } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    form_data: Object,
    business_seetings: Object,
});

const form = useForm({
    fap_status: props.form_data.fap_status,
    fap_max: props.form_data.fap_max,
});

const submitForm = () => {
    form.put(route("partner.settings.fap"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="submitForm">
        <template #description>
            Fair access policy helps preventing usage abuse on unlimited memberships.
            When enabled, members having unlimited memberships will not be able to book more classes for given day than defined by policy.
        </template>

        <template #form>
            <!-- Max -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="fap_max" value="Max number of classes member can book" />
                <TextInput
                    id="max"
                    v-model="form.fap_max"
                    type="number"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.fap_max" class="mt-2" />
            </div>


            <!-- Show timezone in store -->
            <div class="col-span-6 sm:col-span-4">
                <Switcher
                    v-model="form.fap_status"
                    title="Status"
                    description="Enable or disable the policy."
                />
                <InputError :message="form.errors.fap_status" class="mt-2" />
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

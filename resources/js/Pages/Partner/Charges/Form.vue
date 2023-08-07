<script setup>
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="charge_name" value="Charge Name" />
                <span>The charge name will be shown on the invoice</span>
                <TextInput
                    id="charge_name"
                    v-model="form.charge_name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g. VAT"
                />
                <InputError :message="form.errors.charge_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    for="charge_percentage_value"
                    value="Charge Percentage Value"
                />
                <TextInput
                    id="charge_percentage_value"
                    v-model="form.charge_percentage_value"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="0.00"
                />
                <InputError
                    :message="form.errors.charge_percentage_value"
                    class="mt-2"
                />
            </div>
            <!-- Off-peak -->
            <div class="">
                <Switcher
                    v-model="form.is_default_charge"
                    title="Default Charge"
                    description="A default charge auto-applies on the checkout, and cannot be removed"
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

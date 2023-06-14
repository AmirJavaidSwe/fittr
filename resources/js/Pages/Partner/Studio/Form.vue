<script setup>

import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";

defineProps({
    form: {
        type: Object,
        required: true
    },
    submitted: {
        type: Function,
        required: true
    }
})

</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title"/>
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    />
                <InputError :message="form.errors.title" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="location" value="Location"/>
                <SelectInput
                    id="location"
                    v-model="form.location"
                    :options="[]"
                    option_value="id"
                    option_text="name"
                    class="mt-1 block w-full"
                >
                </SelectInput>
                <InputError :message="form.errors.location" class="mt-2"/>
            </div>

            <!-- <div class="col-span-6 sm:col-span-4">
                <InputLabel for="ordering" value="Ordering"/>
                <TextInput
                    id="ordering"
                    v-model="form.ordering"
                    type="number"
                    class="mt-1 block w-full"
                    />
                <InputError :message="form.errors.ordering" class="mt-2"/>
            </div> -->
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>

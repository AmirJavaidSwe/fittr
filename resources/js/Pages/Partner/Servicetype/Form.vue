<script setup>
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import Multiselect from "@vueform/multiselect";
import ImageCropper from "@/Components/ImageCropper.vue";

defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    statuses: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div>
                <InputLabel for="title" value="Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <div>
                <InputLabel for="description" value="Description" />
                <TextArea
                    id="description"
                    v-model="form.description"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <div>
                <InputLabel for="status" value="Status" />
                <Multiselect
                    id="status"
                    v-model="form.status"
                    :options="statuses"
                    :close-on-select="true"
                    show-labels="true"
                    placeholder="Status"
                    >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label">
                            <ColoredValue :color="value.color" :title="value.label" />
                        </div>
                    </template>
                    <template v-slot:option="{ option }">
                        <ColoredValue :color="option.color" :title="option.label" />
                    </template>
                </Multiselect>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <div>
                <InputLabel for="image" value="Image" />
                <ImageCropper v-model="form.image" />
                <InputError :message="form.errors?.image" class="mt-2" />
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

<script setup>
import { computed, watch } from "vue";
import { DateTime } from "luxon";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import Switcher from "@/Components/Switcher.vue";
import Avatar from "@/Components/Avatar.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Dropzone from "@/Components/Dropzone.vue";


const emit = defineEmits([
    'removeFile'
]);
const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    business_seetings: Object,
});

const formatDate = computed(() => {
    return props.business_seetings?.date_format?.format_js
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="">
                <InputLabel for="dob" value="Date of Birth" />
                <Datepicker
                    class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                    v-model="form.date_of_birth"
                    :enable-time-picker="false"
                    :max-date="new Date()"
                    :format="formatDate"
                    placeholder="Date of Birth"
                    text-input
                    auto-apply
                />
                <InputError :message="form.errors.date_of_birth" class="mt-2" />
            </div>

            <div class="my-3">
                <InputLabel for="image" value="Image" />
                <Dropzone
                    id="header-image"
                    v-model="form.profile_photo"
                    :uploaded_file="form.profile_photo_url ? form.profile_photo_url : ''"
                    :accept="['.jpg', '.jpeg', '.png', '.webp']"
                    :modal="true"
                    :max_width="200"
                    :max_height="200"
                    :buttonText="'Select new profile photo'"
                    :instance_name="'profile_photo'"
                    @remove_file="$emit('removeFile')"
                />
                <InputError :message="form.errors.profile_photo" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="submit"
            >
                <span v-if="!form.id">Add Family Member</span>
                <span v-else>Save Changes</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>

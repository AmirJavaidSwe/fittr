<script setup>
import { computed, watch } from "vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import SelectInput from "@/Components/SelectInput.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Multiselect from "@vueform/multiselect";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faClose, faPlus } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true
    },
    locations: {
        type: Array,
        required: true
    },
    class_types: Array,
});

const emit = defineEmits(["createNewLocation"]);
const locationChanged = () => {
    if(props.form.location_id == 'create_new_location') {
        emit('createNewLocation');
    }
}

const locationsList = computed(() => {
    let locations = [];
    for (let i = 0; i < props.locations.length; i++) {
        locations.push({
            value: props.locations[i].id,
            label: props.locations[i].title,
        });
    }
    return locations;
})

const isEdit = computed(() => {
    return props.form?.id ? true : false;
});

</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="location" value="Location"/>
                <Multiselect mode="single"
                    id="location"
                    v-model="form.location_id"
                    :options="locationsList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Location"
                    @select="locationChanged"
                    autocomplete="off"
                />
                <InputError :message="form.errors.location_id" class="mt-2"/>
            </div>

            <div v-if="isEdit">
                <div class="mt-4 font-bold">Spaces</div>
                <div v-for="(class_type_studio, index) in form.class_type_studios" class="rounded-md p-3 lg:text-base border border-gray-300 mt-4 relative">
                    <div class="absolute top-3 right-3">
                        <!-- <div class="flex flex-grow font-bold">Space</div> -->
                        <FontAwesomeIcon class="cursor-pointer" :icon="faClose" @click="e => form.class_type_studios.splice(index, 1)" />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Class Type" />
                        <Multiselect
                            v-model="class_type_studio.class_type_id"
                            :options="class_types?.filter((item) => {
                                return form.class_type_studios
                                    .map((cts) => cts.class_type_id)
                                    .indexOf(item.value) < 0 || item.value == class_type_studio.class_type_id;
                            })"
                            :searchable="true"
                            :close-on-select="true"
                            :show-labels="true"
                            placeholder="Select Class Type"
                        >
                            <template v-slot:singlelabel="{ value }">
                                <div class="multiselect-single-label flex items-center">
                                    <span class="ml-2">{{ value.label }}</span>
                                </div>
                            </template>

                            <template v-slot:option="{ option }">
                                <span class="ml-5">{{ option.label }}</span>
                            </template>
                        </Multiselect>
                        <InputError :message="form.errors[`class_type_studios.${index}.class_type_id`]" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Number of Places" />
                        <TextInput
                            v-model="class_type_studio.spaces"
                            type="number"
                            class="mt-1 block w-full"
                            min="1"
                            max="1000"
                        />
                        <InputError :message="form.errors[`class_type_studios.${index}.spaces`]" class="mt-2" />
                    </div>
                </div>
                <div class="rounded-md p-3 lg:text-base border-2 border-dashed border-blue-500 bg-blue-100 text-blue-500 flex items-center justify-center cursor-pointer mt-4" @click="e => form.class_type_studios.push({class_type_id: null, spaces: null})">
                    <FontAwesomeIcon :icon="faPlus" />
                    <span class="ml-1 font-semibold">Add Space</span>
                </div>
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

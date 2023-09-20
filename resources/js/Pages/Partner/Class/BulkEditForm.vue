<script setup>
import { ref, computed } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import Avatar from "@/Components/Avatar.vue";
import TextInput from "@/Components/TextInput.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";

const emit = defineEmits([
    "createNewInstructor",
    "createNewClassType",
    "createNewStudio",
]);
const props = defineProps({
    statuses: Object,
    instructors: Object,
    classtypes: Object,
    studios: Object,
    business_settings: Object,
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    isNew: {
        type: Boolean,
        default: false,
        required: false,
    },
    isDuplicate: {
        type: Boolean,
        default: false,
        required: false,
    },
});

const instructorChanged = () => {
    if (props.form.instructor_id == "create_new_instructor") {
        emit("createNewInstructor");
    }
};
const classTypeChanged = () => {
    if (props.form.class_type_id == "create_new_class_type") {
        emit("createNewClassType");
    }
};
const studioChanged = () => {
    if (props.form.studio_id == "create_new_studio") {
        emit("createNewStudio");
    }
};

const showPassword = ref(false);
const inputPasswordType = computed(() =>
    showPassword.value ? "text" : "password"
);

const instructorsList = computed(() => {
    let newInstructorsList = { ...props.instructors }; // Create a shallow copy of the object
    newInstructorsList.create_new_instructor = "Add New"; // Add a new property
    return newInstructorsList;
});

const classTypeList = computed(() => {
    let newClassTypeList = { ...props.classtypes }; // Create a shallow copy of the object
    newClassTypeList.create_new_class_type = "Add New"; // Add a new property
    return newClassTypeList;
});

const studiosOptions = computed(() => {
    return props.studios.map(item => ({value: item.id, label: item.title}));
});

const studioList = computed(() => {
    let newStudioList = studiosOptions.value;
    newStudioList.push({
        'label': "Add New",
        'value': "create_new_studio"
    })
    return newStudioList;
});

const defaultCapacity = computed(() => {
    const studio = props.studios.filter(item => props.form.studio_id == item.id)[0];
    if(studio) {
        const classTypeStudio = studio.class_type_studios.filter(item => props.form.class_type_id == item.class_type_id)[0];
        return classTypeStudio?.spaces ?? 0;
    }
    return 0;
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <!-- Status -->
            <div class="">
                <InputLabel for="status" value="Status" />
                <Multiselect
                    :autocomplete="'off'"
                    v-model="form.status"
                    :options="statuses"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Status"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <ColoredValue color="#ddd" :title="value.label" />
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <ColoredValue color="#ddd" :title="option.label" />
                    </template>
                </Multiselect>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Instructors -->
            <div class="">
                <InputLabel for="instructors" value="Instructors" />
                <Multiselect
                    :autocomplete="'off'"
                    mode="tags"
                    v-model="form.instructor_id"
                    :options="instructorsList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Instructors"
                    @select="instructorChanged"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <Avatar
                                size="small"
                                :title="value.label"
                                v-if="value.label != 'Add New'"
                            />
                            <span class="ml-2">{{ value.label }}</span>
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <Avatar
                            size="small"
                            :title="option.label"
                            v-if="option.label != 'Add New'"
                        />
                        <span class="ml-5">{{ option.label }}</span>
                    </template>
                </Multiselect>
                <InputError :message="form.errors.instructor_id" class="mt-2" />
            </div>

            <!-- Class Type -->
            <div class="">
                <InputLabel for="classtype" value="Class Type" />
                <Multiselect
                    :autocomplete="'off'"
                    v-model="form.class_type_id"
                    :options="classTypeList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Class Type"
                    @select="classTypeChanged"
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
                <InputError :message="form.errors.class_type_id" class="mt-2" />
            </div>

            <!-- Studios -->
            <div class="">
                <InputLabel for="studios" value="Studio" />
                <Multiselect
                    :autocomplete="'off'"
                    v-model="form.studio_id"
                    :options="studioList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                    @select="studioChanged"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <MapMarker v-if="value.label != 'Add New'" />
                            <span class="ml-2">{{ value.label }}</span>
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <MapMarker v-if="option.label != 'Add New'" />
                        <span class="ml-2">{{ option.label }}</span>
                    </template>
                </Multiselect>
                <InputError :message="form.errors.studio_id" class="mt-2" />
            </div>

            <div class="">
                <InputLabel
                    value="Class Capacity"
                    class="mb-2"
                />

                <div class="flex justify-between mt-1 items-center">
                    <div v-if="form.use_defaults" class="mr-2">Class pre-defined spaces</div>
                    <ButtonLink v-if="form.use_defaults" type="button" size="small" styling="secondary" @click="form.use_defaults = false">Change</ButtonLink>
                    <TextInput
                        v-else
                        id="title"
                        v-model="form.spaces"
                        type="number"
                        min="1"
                        max="1000"
                        class="mr-2 block w-full"
                    />
                </div>


            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Your Password" class="mb-1" />
                <div class="relative">
                    <TextInput
                        :type="inputPasswordType"
                        id="password"
                        v-model="form.password"
                        class="mt-1 block w-full"
                        autocomplete="off"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 focus:outline-none"
                    >
                        <template v-if="showPassword">
                            <font-awesome-icon
                                :icon="faEyeSlash"
                                style="color: #b0b2b5"
                            />
                        </template>
                        <template v-else>
                            <font-awesome-icon
                                style="color: #4ca054"
                                :icon="faEye"
                                class="blue-grey-50"
                            />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
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
                <span v-if="isNew">Create</span>
                <span v-else-if="isDuplicate">Duplicate</span>
                <span v-else>Save changes</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>

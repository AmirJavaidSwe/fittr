<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEye, faEyeSlash, faCircleQuestion } from "@fortawesome/free-solid-svg-icons";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Multiselect from "@vueform/multiselect";
import Avatar from "@/Components/Avatar.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import "@vueform/multiselect/themes/tailwind.css";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    }
});

const emit = defineEmits(["createNewRole"]);

const roleChanged = () => {
    if (props.form.roles.includes("create_new_role")) {
        emit("createNewRole");
    }
};

const rolesList = computed(() => {
    let roles = [];
    for (let i = 0; i < props.roles.length; i++) {
        roles.push({
            value: props.roles[i].id,
            label: props.roles[i].title,
        });
    }
    return roles;
});

const showPassword = ref(false);
const inputPasswordType = computed(() =>
    showPassword.value ? "text" : "password"
);

const showPasswordField = ref(false);
const newPasswordLabel = computed(() =>
    showPasswordField.value ? "Set new password" : "Keep current password"
);
</script>

<template>
    <FormSection @submitted="submitted">
        <template #title></template>
        <template #description></template>
        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="email"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="flex flex-row items-center justify-start">
                <Checkbox
                    id="is_super_admin"
                    v-model="form.is_super"
                    :checked="form.is_super"
                    class="mt-3 mr-2 mb-3"
                >
                </Checkbox>
                <InputLabel
                    for="is_super_admin"
                    value="Is Super Admin?"
                    class="mr-3 w-full"
                />
            </div>
            <div class="col-span-6 sm:col-span-4" v-if="!form.is_super">
                <InputLabel for="roles" value="Select Roles" />
                <Multiselect
                    mode="tags"
                    v-model="form.roles"
                    :options="rolesList"
                    @select="roleChanged"
                />
                <InputError :message="form.errors.roles" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="inline-flex items-center" @click="showPasswordField = !showPasswordField">

                    <InputLabel @click="showPasswordField = !showPasswordField"
                    for="password"
                    :value="newPasswordLabel"
                    class="cursor-pointer" />
                    <font-awesome-icon
                    :icon="faCircleQuestion"
                    class="ml-1 text-dark-600 cursor-pointer"
                    v-tooltip="showPasswordField ? 'Click to hide new password field' : 'Click to show new password field'"
                    />
                </div>
                <div class="relative" v-if="showPasswordField">
                    <TextInput
                        id="password"
                        v-model="form.password"
                        :type="inputPasswordType"
                        class="mt-1 block w-full"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none"
                    >
                        <template v-if="showPassword">
                            <font-awesome-icon
                                :icon="faEye"
                                class="text-green-600"
                            />
                        </template>
                        <template v-else>
                            <font-awesome-icon :icon="faEyeSlash" />
                        </template>
                    </button>
                </div>
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Updated.
            </ActionMessage>
            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Update
            </ButtonLink>
        </template>
    </FormSection>
</template>

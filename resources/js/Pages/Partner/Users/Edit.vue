<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Multiselect from "@vueform/multiselect";
import _ from "lodash";

const roles = ref([]);
const props = defineProps({
    editUser: Object,
    roles: Object,
});
const form = useForm({
    id: props.editUser.id,
    name: props.editUser.name,
    email: props.editUser.email,
    is_super: props.editUser.is_super,
    password: null,
});

const updateUser = () => {
    form.transform((data) => ({
        ...data,
        roles: data.is_super ? [] : roles.value,
        is_super: data.is_super,
    })).put(route("partner.users.update", { id: props.editUser.id }), {
        preserveScroll: true,
    });
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

onMounted(() => {
    if (props.editUser.roles.length) {
        const ids = _.map(props.editUser.roles, "id");
        roles.value = ids;
    }
});

const showPassword = ref(false);
const inputPasswordType = computed(() =>
    showPassword.value ? "text" : "password"
);
const showPasswordField = ref(false);
const newPasswordLabel = computed(() =>
    showPasswordField.value ? "Keep current password" : "Set new password"
);
</script>
<template>
    <FormSection @submitted="updateUser">
        <template #title> User Information </template>
        <template #description> Update user information. </template>
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
            <template v-if="$page.props.user.id != form.id">
                <div class="flex flex-row items-center justify-start">
                    <Checkbox
                        id="is_super_admin"
                        v-model="form.is_super"
                        :checked="form.is_super"
                        class="mt-3 mr-2 mb-3"
                    ></Checkbox>
                    <InputLabel
                        for="is_super_admin"
                        value="Is Super Admin?"
                        class="mr-3 w-full"
                    />
                </div>
                <div class="col-span-6 sm:col-span-4" v-if="!form.is_super">
                    <div v-if="!props.roles.length">
                        You don't have any roles, please create new role and
                        come back
                        <ButtonLink
                            :href="route('partner.roles.create')"
                            type="primary"
                            >Add new</ButtonLink
                        >
                    </div>
                    <div v-else>
                        <InputLabel for="roles" value="Select Roles" />
                        <Multiselect
                            mode="tags"
                            v-model="roles"
                            :options="rolesList"
                        />
                        <InputError :message="form.errors.roles" class="mt-2" />
                    </div>
                </div>
            </template>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    @click="showPasswordField = !showPasswordField"
                    for="password"
                    :value="newPasswordLabel"
                    class="cursor-pointer"
                />
                <div v-if="showPasswordField" class="relative">
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
                            <font-awesome-icon :icon="faEyeSlash" />
                        </template>
                        <template v-else>
                            <font-awesome-icon
                                :icon="faEye"
                                class="text-green-600"
                            />
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

<style src="@vueform/multiselect/themes/default.css"></style>

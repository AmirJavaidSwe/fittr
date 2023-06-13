<script setup>
import { ref, watch, watchEffect, onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { RoleHelpers } from "./RoleHelpers/Index.js";
import FormSection from "@/Components/FormSection.vue";
import Section from "@/Components/Section.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    role: Object,
    modules: Object,
});
const permissions = ref([]);
const roleHelpers = RoleHelpers();
const allPermissionIds = ref(roleHelpers.collectAllPermissions(props.modules));
const rolePermissions = ref([]);
const allowAllPermissions = ref(false);
const form = useForm({
    title: props.role.title,
    slug: props.role.slug,
});
const togglePermission = (permissionId) => {
    const data = roleHelpers.togglePermission(
        permissions.value,
        permissionId,
        allPermissionIds.value
    );
    permissions.value = data.permissions;
    allowAllPermissions.value = data.allowAllPermissions;
};
const toggleAllPermission = () => {
    const data = roleHelpers.toggleAllPermission(
        allowAllPermissions.value,
        props.modules
    );
    if (data.permissions) {
        permissions.value = data.permissions;
    }
    allowAllPermissions.value = data.allowAllPermissions;
};
const updateRole = () => {
    form.transform((data) => ({
        ...data,
        permissions: permissions.value,
    })).put(
        route(`${usePage().props.user.source}.roles.update`, {
            role: props.role.slug,
        }),
        {
            preserveScroll: true,
        }
    );
};
const roleHasPermission = () => {
    const data = roleHelpers.roleHasPermission(
        rolePermissions.value,
        permissions.value,
        props.role
    );
    rolePermissions.value = data.rolePermissions;
    permissions.value = data.permissions;
};
onMounted(() => {
    roleHasPermission();
    if (permissions.value.length == allPermissionIds.value.length) {
        allowAllPermissions.value = true;
    }
});
</script>
<template>
    <FormSection @submitted="updateRole">
        <template #title> Role Information </template>
        <template #description> Update role information. </template>
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Role title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>
            <Section>
                <SectionTitle>
                    <template #title> Permissions </template>
                </SectionTitle>
                <div class="mt-5 flex flex-row items-center justify-start">
                    <Checkbox
                        id="allow-all"
                        :checked="allowAllPermissions"
                        @change="toggleAllPermission"
                        class="ml-2 mr-2"
                    >
                    </Checkbox>
                    <InputLabel
                        for="allow-all"
                        value="Allow All"
                        class="mr-3"
                    />
                </div>
                <SectionBorder />
                <dl
                    class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-gray-900 dark:divide-gray-700"
                >
                    <template
                        v-for="system_module in props.modules"
                        :key="system_module.id"
                    >
                        <div class="flex flex-col pb-5 mb-5">
                            <dt
                                class="mt-2 mb-1 text-gray-500 md:text-lg dark:text-gray-400"
                            >
                                {{ system_module.title }}
                            </dt>
                            <div
                                class="flex flex-row items-center justify-start"
                            >
                                <template
                                    v-for="permission in system_module.permissions"
                                    :key="permission.id"
                                >
                                    <Checkbox
                                        :disabled="allowAllPermissions"
                                        :id="
                                            system_module.slug +
                                            '-' +
                                            permission.slug
                                        "
                                        :checked="
                                            permissions.includes(permission.id)
                                        "
                                        @change="
                                            togglePermission(permission.id)
                                        "
                                        class="ml-2 mr-2"
                                        :class="[
                                            allowAllPermissions && 'opacity-50',
                                        ]"
                                    >
                                    </Checkbox>
                                    <InputLabel
                                        :for="
                                            system_module.slug +
                                            '-' +
                                            permission.slug
                                        "
                                        :value="permission.title"
                                        class="mr-3"
                                    />
                                </template>
                            </div>
                        </div>
                    </template>
                </dl>
                <InputError :message="form.errors.permissions" class="mt-2" />
            </Section>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Updated.
            </ActionMessage>
            <ButtonLink
                size="default"
                styling="primary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Update
            </ButtonLink>
        </template>
    </FormSection>
</template>

<script setup>
import { ref, watch, watchEffect, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { RoleHelpers } from "./RoleHelpers/Index.js";
const props = defineProps({
    role: Object,
    modules: Object,
});
const permissions = ref([])
const roleHelpers = RoleHelpers()
const allPermissionIds = ref(roleHelpers.collectAllPermissions(props.modules))
const rolePermissions = ref([])
const allowAllPermissions  = ref(false)
const form = useForm({
    name: props.role.name,
    slug: props.role.slug,
});
const togglePermission = (permissionId) => {
    const data = roleHelpers.togglePermission(permissions.value, permissionId, allPermissionIds.value)
    permissions.value = data.permissions
    allowAllPermissions.value = data.allowAllPermissions
}
const toggleAllPermission = () => {
    const data = roleHelpers.toggleAllPermission(allowAllPermissions.value, props.modules)
    if(data.permissions) {
        permissions.value = data.permissions
    }
    allowAllPermissions.value = data.allowAllPermissions
}
const updateRole = () => {
    form.transform((data) => ({
        ...data,
        permissions: permissions.value
    })).put(route('admin.roles.update', { role: props.role.slug }), {
        preserveScroll: true
    });
};
const roleHasPermission = () => {
    const data = roleHelpers.roleHasPermission(rolePermissions.value, permissions.value, props.role)
    rolePermissions.value = data.rolePermissions
    permissions.value = data.permissions
}
onMounted(() => {
    roleHasPermission()
    if(permissions.value.length == allPermissionIds.value.length) {
        allowAllPermissions.value = true
    }
})
</script>
<template>
    <FormSection @submitted="updateRole">
        <template #title>
            Role Information
        </template>
        <template #description>
            Update role information.
        </template>
        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autocomplete="name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <Section>
                <SectionTitle>
                    <template #title>
                        Permissions
                    </template>
                </SectionTitle>
                <div class="mt-5 flex flex-row items-center justify-start">
                    <Checkbox id="allow-all" :checked="allowAllPermissions" @change="toggleAllPermission" class="ml-2 mr-2">
                    </Checkbox>
                    <InputLabel for="allow-all" value="Allow All" class="mr-3" />
                </div>
                <SectionBorder />
                <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-gray-900 dark:divide-gray-700">
                    <template v-for="module in props.modules" :key="module.id">
                        <div class="flex flex-col pb-5 mb-5">
                            <dt class="mt-2 mb-1 text-gray-500 md:text-lg dark:text-gray-400">{{ module.name }}</dt>
                            <div class="flex flex-row items-center justify-start">
                                <template v-for="permission in module.permissions" :key="permission.id">
                                    <Checkbox :disabled="allowAllPermissions" :id="module.slug + '-' + permission.slug"
                                        :checked="permissions.includes(permission.id)"
                                        @change="togglePermission(permission.id)" class="ml-2 mr-2"
                                        :class="[allowAllPermissions && 'opacity-50']">
                                    </Checkbox>
                                    <InputLabel :for="module.slug + '-' + permission.slug" :value="permission.name"
                                        class="mr-3" />
                                </template>
                            </div>
                        </div>
                    </template>
                </dl>
            </Section>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Updated.
            </ActionMessage>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Update
            </PrimaryButton>
        </template>
    </FormSection>
</template>

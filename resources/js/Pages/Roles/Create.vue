<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { RoleHelpers } from "./RoleHelpers/Index.js";

const props = defineProps({
    modules: Object,
});

const permissions = ref([])
const roleHelpers = RoleHelpers()
const allowAllPermissions = ref(false)

const allPermissionIds = ref(roleHelpers.collectAllPermissions(props.modules))

const form = useForm({
    title: null,
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

const create = () => {
    form.transform((data) => ({
        ...data,
        permissions: permissions.value
    })).post(route(`${usePage().props.user.source}.roles.store`), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

</script>

<template>
    <FormSection @submitted="create">
        <template #title>
            Role Information
        </template>

        <template #description>
            <div>Create new role.</div>
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title" />
                <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" />
                <InputError :message="form.errors.title" class="mt-2" />
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
                    <template v-for="system_module in props.modules" :key="system_module.id">
                        <div class="flex flex-col pb-5 mb-5">
                            <dt class="mt-2 mb-1 text-gray-500 md:text-lg dark:text-gray-400">{{ system_module.title }}</dt>
                            <div class="flex flex-row items-center justify-start">
                                <template v-for="permission in system_module.permissions" :key="permission.id">
                                    <Checkbox :id="system_module.slug + '-' + permission.slug" :value="permission.id"
                                        :checked="permissions.includes(permission.id)"
                                        @change="togglePermission(permission.id)" class="ml-2 mr-2">
                                    </Checkbox>
                                    <InputLabel :for="system_module.slug + '-' + permission.slug" :value="permission.title" class="mr-3 w-full" />
                                </template>
                            </div>
                        </div>

                    </template>
                </dl>
            </Section>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Role has been created.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>

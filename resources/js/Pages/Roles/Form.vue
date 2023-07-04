<script setup>
import { ref, computed } from "vue";
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
    form: {
        type: Object,
        required: true,
    },
    systemModules: {
        type: Array,
        required: true
    }
});

const permissions = ref([]);
const title = ref(null);
const roleHelpers = RoleHelpers();
const allowAllPermissions = ref(false);

const allPermissionIds = ref(roleHelpers.collectAllPermissions(props.systemModules));

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
        props.systemModules
    );
    if (data.allowAllPermissions == false) {
        permissions.value = [];
    }
    else if (data.permissions) {
        permissions.value = data.permissions;
    }
    allowAllPermissions.value = data.allowAllPermissions;
};
const emit = defineEmits(['submitted'])
const create = () => {
    emit('submitted', {
        title: title.value,
        permissions: permissions.value
    })
};
</script>

<template>
    <FormSection @submitted="create">
        <template #title></template>

        <template #description>
            <div></div>
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title" />
                <TextInput
                    id="title"
                    v-model="title"
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

                
                    <template
                        v-for="system_module in props.systemModules"
                        :key="system_module.id"
                    >
                        <div class="grid pb-5 mb-5">
                            <strong>

                                {{ system_module.title }}
                            </strong>
                            <div
                                class="grid grid-cols-6 gap-6 items-center justify-start mt-4"
                            >
                                <template
                                    v-for="permission in system_module.permissions"
                                    :key="permission.id"
                                >
                                <div class="flex flex-col justify-start ">

                                    <Checkbox
                                    :id="
                                            system_module.slug +
                                            '-' +
                                            permission.slug
                                        "
                                        :value="permission.id"
                                        :checked="permissions.includes(permission.id)"
                                        @change="
                                            togglePermission(permission.id)
                                        "
                                        class=""
                                    >
                                    </Checkbox>
                                    <InputLabel
                                        :for="
                                            system_module.slug +
                                            '-' +
                                            permission.slug
                                            "
                                        :value="permission.title"
                                        class="mr-3 w-full"
                                        />
                                    </div>
                                    </template>
                            </div>
                        </div>
                    </template>
                <InputError :message="form.errors.permissions" class="mt-2" />
            </Section>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Role has been created.
            </ActionMessage>

            <ButtonLink
                size="default"
                styling="secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>
</template>

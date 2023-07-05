<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import FormSection from "@/Components/FormSection.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import _ from "lodash";

const roles = ref([]);

const props = defineProps({
    roles: Object,
});
const form = useForm({
    name: "",
    email: "",
    password: "",
    is_super: true,
});

const addAdmin = () => {
    form.transform((data) => ({
        ...data,
        roles: data.is_super ? [] : roles.value,
        is_super: data.is_super === true ? 1 : 0,
    })).post(route("admin.settings.save.admins"), {
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

onMounted(() => {});
</script>
<template>
    <FormSection @submitted="addAdmin">
        <template #title> Admin Information </template>
        <template #description> Add admin information. </template>
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
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
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
                <InputLabel for="roles" value="Select Roles" />
                <Multiselect mode="tags" v-model="roles" :options="rolesList" />
            </div>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Created
            </ActionMessage>
            <ButtonLink
                styling="primary"
                size="small"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Add
            </ButtonLink>
        </template>
    </FormSection>
</template>

<style src="@vueform/multiselect/themes/tailwind.css"></style>

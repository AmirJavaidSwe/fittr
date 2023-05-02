<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Multiselect from '@vueform/multiselect';
import _ from "lodash";


const roles = ref([])

const props = defineProps({
    admin: Object,
    roles: Object,
});
const form = useForm({
    name: props.admin.name,
    email: props.admin.email,
    id: props.admin.id,
});

const updateAdmin = () => {
    form.transform((data) => ({
        ...data,
        roles: roles.value
    })).put(route('admin.settings.update.admins', { id: props.admin.id }), {
        preserveScroll: true
    });
};

const rolesList = computed(() => {
    let roles = []
    for(let i = 0; i < props.roles.length; i++) {
        roles.push({
            value: props.roles[i].id,
            label: props.roles[i].name
        })
    }
    return roles
})

onMounted(() => {
    if(props.admin.roles.length) {
        const ids = _.map(props.admin.roles, "id");
        roles.value = ids
    }
})
</script>
<template>
    <FormSection @submitted="updateAdmin">
        <template #title>
            Admin Information
        </template>
        <template #description>
            Update admin information.
        </template>
        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autocomplete="name" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="text" class="mt-1 block w-full" autocomplete="email" />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="roles" value="Select Roles" />
                <Multiselect mode="tags" v-model="roles" :options="rolesList" />
            </div>
            
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

<style src="@vueform/multiselect/themes/default.css"></style>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Multiselect from '@vueform/multiselect';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import FormSection from "@/Components/FormSection.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import _ from "lodash";


const roles = ref([])

const props = defineProps({
    admin: Object,
    roles: Object,
});
const form = useForm({
    first_name: props.admin.first_name,
    last_name: props.admin.last_name,
    email: props.admin.email,
    id: props.admin.id,
    is_super: props.admin.is_super ? true : false,
});

const updateAdmin = () => {
    form.transform((data) => ({
        ...data,
        roles: data.is_super ? [] : roles.value,
        is_super: data.is_super === true ? 1 : 0
    })).put(route('admin.settings.update.admins', { id: props.admin.id }), {
        preserveScroll: true
    });
};

const rolesList = computed(() => {
    let roles = []
    for(let i = 0; i < props.roles.length; i++) {
        roles.push({
            value: props.roles[i].id,
            label: props.roles[i].title
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
            <!-- First Name -->
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" autocomplete="name" />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div>
            <!-- Last Name -->
            <div>
                <InputLabel for="last_name" value="First Name" />
                <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" autocomplete="name" />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div>
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="text" class="mt-1 block w-full" autocomplete="email" />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>
            <template v-if="!($page.props.user.id == form.id)">
                <div class="flex flex-row items-center justify-start">
                        <Checkbox id="is_super_admin" v-model="form.is_super" :checked="form.is_super" class="mt-3 mr-2 mb-3"></Checkbox>
                        <InputLabel for="is_super_admin" value="Is Super Admin?" class="mr-3 w-full" />
                </div>
                <div class="col-span-6 sm:col-span-4" v-if="!form.is_super">
                    <InputLabel for="roles" value="Select Roles" />
                    <Multiselect mode="tags" v-model="roles" :options="rolesList" />
                </div>
            </template>
            
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Updated.
            </ActionMessage>
            <ButtonLink
                styling="primary"
                size="small"
                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Update
            </ButtonLink>
        </template>
    </FormSection>
</template>
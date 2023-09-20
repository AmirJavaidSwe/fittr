<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SideModal from '@/Components/SideModal.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye,faEyeSlash } from '@fortawesome/free-solid-svg-icons';
import ButtonLink from '@/Components/ButtonLink.vue';
import CloseModal from '@/Components/CloseModal.vue';


const show = ref(usePage().props?.extra?.show_password_creation ?? false);

const close = () => {
    show.value = false;
}

const form = useForm({
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const inputPasswordType = computed(() => (showPassword.value ? "text" : "password"));
const inputConfirmPasswordType = computed(() => (showConfirmPassword.value ? "text" : "password"));

const createPassword = () => {
    form.post(route('password.creation'), {
        onSuccess: () => {
            form.reset('password', 'password_confirmation');
            close();
        },
    });
}

</script>

<template>
    <SideModal :show="show" @close="close">
        <template #title> Create New Password </template>
        <template #close>
            <CloseModal @click="close" />
        </template>

        <template #content>
            <div class="mb-4">
                <InputLabel for="password" value="New Password" class="mb-1" />
                <div class="relative border-none p-0">
                    <TextInput id="password" v-model="form.password" :type="inputPasswordType"
                        class="mt-1 block w-full" required autocomplete="new-password" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none">
                        <template v-if="showPassword">
                            <font-awesome-icon :icon="faEyeSlash" />
                        </template>
                        <template v-else>
                            <font-awesome-icon :icon="faEye" class="text-green-600" />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mb-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative border-none p-0">
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" :type="inputConfirmPasswordType" class="mt-1 block w-full" required autocomplete="new-password" />
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none">
                        <template v-if="showConfirmPassword">
                            <font-awesome-icon :icon="faEyeSlash" />
                        </template>
                        <template v-else>
                            <font-awesome-icon :icon="faEye" class="text-green-600" />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
        </template>
        <template #footer>
            <ButtonLink
                styling="default"
                size="default"
                class="mr-2"
                @click="close"
            >
                Skip
            </ButtonLink>
            <ButtonLink
                styling="secondary"
                size="default"
                :disabled="form.processing"
                @click="createPassword"
            >
                Reset Password
            </ButtonLink>
        </template>
    </SideModal>
</template>
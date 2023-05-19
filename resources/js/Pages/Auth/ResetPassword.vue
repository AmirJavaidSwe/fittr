<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye,faEyeSlash } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const inputPasswordType = computed(() => (showPassword.value ? "text" : "password"));
const inputConfirmPasswordType = computed(() => (showConfirmPassword.value ? "text" : "password"));
</script>

<template>
    <Head title="Reset Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" class="mb-1" />
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

            <div class="mt-4">
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

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Reset Password
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>

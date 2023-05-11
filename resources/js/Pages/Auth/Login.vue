<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GoogleIcon from '@/Icons/Google.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye,faEyeSlash } from '@fortawesome/free-solid-svg-icons';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const input = ref(null);
const showPassword = ref(false);

const inputType = computed(() => (showPassword.value ? "text" : "password"));

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
    input.value.type = showPassword.value ? "text" : "password";
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" class="mb-1" />
                <div class="relative border-none p-0">
                    <TextInput ref="input" :type="inputType" id="password" v-model="form.password" class="mt-1 block w-full" required autocomplete="current-password" />
                    <button type="button" @click="togglePasswordVisibility"
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

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900">
                Forgot your password?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>

            <div class="flex flex-col gap-4 mt-8">
                <div class="flex items-center">
                    <div class="flex-grow bg bg-gray-300 h-px"></div>
                    <div class="flex-grow-0 mx-5 text">OR</div>
                    <div class="flex-grow bg bg-gray-300 h-px"></div>
                </div>
                <a :href="route('auth.google')" class="bg-white border flex font-medium p-4 rounded text-center w-full">
                    <GoogleIcon />
                    <div class="flex-grow -ml-6">Continue with Google</div>
                </a>
            </div>
        </form>
    </AuthenticationCard>
</template>

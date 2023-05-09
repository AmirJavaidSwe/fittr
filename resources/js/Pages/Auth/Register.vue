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
import Eye from "@/Icons/Eye.vue";
import EyeSlash from "@/Icons/EyeSlash.vue";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
const inputPassword = ref(null);
const inputConfirmPassword = ref(null);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const inputPasswordType = computed(() => (showPassword.value ? "text" : "password"));
const inputConfirmPasswordType = computed(() => (showConfirmPassword.value ? "text" : "password"));

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
    inputPassword.value.type = showPassword.value ? "text" : "password";
};
const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
    inputConfirmPassword.value.type = showConfirmPassword.value ? "text" : "password";
};
</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus
                    autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" class="mb-1" />
                <div class="relative border-none p-0">
                    <TextInput ref="inputPassword" id="password" v-model="form.password" :type="inputPasswordType" class="mt-1 block w-full" required
                        autocomplete="new-password" />
                    <button type="button" @click="togglePasswordVisibility"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none">
                        <template v-if="showPassword">
                            <EyeSlash />
                        </template>
                        <template v-else>
                            <Eye />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" class="mb-1" />
                <div class="relative border-none p-0">
                    <TextInput ref="inputConfirmPassword" id="password_confirmation" v-model="form.password_confirmation" :type="inputConfirmPasswordType"
                        class="mt-1 block w-full" required autocomplete="new-password" />
                    <button type="button" @click="toggleConfirmPasswordVisibility"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none">
                        <template v-if="showConfirmPassword">
                            <EyeSlash />
                        </template>
                        <template v-else>
                            <Eye />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('terms.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900">Terms of Service</a> and <a
                                target="_blank" :href="route('policy.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                Already registered?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
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

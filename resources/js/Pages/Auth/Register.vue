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
import AuthBackground from "@/Components/AuthBackground.vue";

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
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const inputPasswordType = computed(() => (showPassword.value ? "text" : "password"));
const inputConfirmPasswordType = computed(() => (showConfirmPassword.value ? "text" : "password"));
</script>

<template>
    <Head title="Register" />
    <div class="flex flex-col lg:flex-row rounded-xl mx-auto min-h-screen">
    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo class="flex justify-center pt-8 sm:pt-0" />
        </template>

        <form @submit.prevent="submit" class="w-96 mx-auto bg-white p-5 rounded-lg max-[500px]:w-full">
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
                <div class="relative">
                    <TextInput id="password" v-model="form.password" :type="inputPasswordType" class="mt-1 block w-full" required
                        autocomplete="new-password" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 focus:outline-none">
                        <template v-if="showPassword">
                            <font-awesome-icon :icon="faEyeSlash" style="color: #b0b2b5"/>
                        </template>
                        <template v-else>
                            <font-awesome-icon :icon="faEye" class="text-dark-600" style="color:#4ca054;" />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" class="mb-1" />
                <div class="relative">
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" :type="inputConfirmPasswordType"
                        class="mt-1 block w-full" required autocomplete="new-password" />
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 focus:outline-none">
                        <template v-if="showConfirmPassword">
                            <font-awesome-icon :icon="faEyeSlash"  style="color:#b0b2b5;" />
                        </template>
                        <template v-else>
                            <font-awesome-icon :icon="faEye" style="color:#4ca054;" class="text-dark-600" />
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

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">By clicking "Create account" or "Countinue with Google", you agree to the  <a target="_blank" :href="test"
                        class="underline text-sm text-gray-600 hover:text-gray-900">Fittr TOS</a> and 
                        <a target="_blank" :href="test"
                        class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>.</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">

                <PrimaryButton class="w-full" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
            <div class="mt-4 flex">
                Already have an account ? <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                      Log in
                    </Link>
            </div>
        </form>
    </AuthenticationCard>
            <AuthBackground>
                    <template #imagetext>
                        <p class="text-white">Lorem ipsum dolor sit amet consectetur. Adipiscing risus dignissim volutpat ut integer malesuada varius fringilla. Id lacus vel lectus viverra id feugiat. Et id sed vel tincidunt amet volutpat vulputate aliquet vitae. Faucibus adipiscing in dui arcu duis. Senectus semper donec dui sit eget ut facilisi ut.</p>
                    </template>
            </AuthBackground>
        </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import GoogleIcon from "@/Icons/Google.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";
import AuthBackground from "@/Components/AuthBackground.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

const showPassword = ref(false);
const inputPasswordType = computed(() =>
    showPassword.value ? "text" : "password"
);
</script>
<style>
   

</style>
<template>
    <Head title="Log in"  sm:text-center/>
    <div class="flex flex-col lg:flex-row rounded-xl mx-auto min-h-screen">
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo class="flex justify-center pt-8 sm:pt-0" />
            </template>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="w-96 mx-auto bg-white p-5 rounded-lg mb-5 max-[500px]:w-full">
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" class="mb-1" />
                    <div class="relative">
                        <TextInput
                            :type="inputPasswordType"
                            id="password"
                            v-model="form.password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 focus:outline-none"
                        >
                            <template v-if="showPassword">
                                <font-awesome-icon :icon="faEyeSlash" style="color: #b0b2b5"/>
                            </template>
                            <template v-else>
                                <font-awesome-icon style="color:#4ca054;"
                                    :icon="faEye"
                                    class="blue-grey-50"
                                />
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

                <ButtonLink
                    styling="secondary"
                    size="default"
                    class="mt-4 w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </ButtonLink>

                <div class="flex flex-col gap-4 mt-8">
                    <div class="flex items-center">
                        <div class="flex-grow bg bg-gray-300 h-px"></div>
                        <div class="flex-grow-0 mx-5 text">Or</div>
                        <div class="flex-grow bg bg-gray-300 h-px"></div>
                    </div>
                    <a :href="route('auth.google')" class="bg-white border flex font-medium p-4 rounded text-center w-full">
                        <div class="flex-grow -ml-6"><GoogleIcon class="inline-block"/> Continue with Google</div>
                    </a>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <ButtonLink :href="route('register')">
                            Create account
                        </ButtonLink>
                        <ButtonLink v-if="canResetPassword" :href="route('password.request')">
                            Forgot password?
                        </ButtonLink>
                    </div>
                </div>
            </form>
        </AuthenticationCard>

        <AuthBackground />
    </div>
</template>

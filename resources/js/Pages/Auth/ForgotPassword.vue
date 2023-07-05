<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import AuthBackground from "@/Components/AuthBackground.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="flex flex-col lg:flex-row rounded-xl mx-auto min-h-screen">
        <AuthenticationCard class="pt-8">
            <template #logo>
                <AuthenticationCardLogo class="flex justify-center pt-8 sm:pt-0" />
            </template>
            <div class="w-96 mx-auto bg-white p-5 rounded-lg max-[500px]:w-full">
                <div class="mb-4 text-sm text-gray-600">
                    <!-- <h3 class="mt-3 mb-3 text-2xl"><strong>Forgot Password?</strong></h3> -->
                    <p>
                        Forgot your password? No problem. Just let us know your email
                        address and we will email you a password reset link that will allow
                        you to choose a new one.
                    </p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">

                    <div class="items-center justify-end mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                            autofocus />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <ButtonLink :href="route('login')">
                            Log in
                        </ButtonLink>
                        <ButtonLink 
                            styling="secondary"
                            size="default"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            >
                            Email Password Reset Link
                        </ButtonLink>

                    </div>
                </form>
            </div>
        </AuthenticationCard>
        <AuthBackground />
    </div>
</template>

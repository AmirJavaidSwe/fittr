<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import AuthBackground from "@/Components/AuthBackground.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};

const logout = () => {
    router.post(route("logout"));
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
                    <h3 class="mt-3 mb-3 text-2xl"><strong>Confirm your e-mail address</strong></h3>
                    <p>Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <ButtonLink
                        styling="primary"
                        size="default"
                        class="w-full justify-center"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        >
                        Resend Verification Email
                    </ButtonLink>
                    <div class="flex items-center mt-4 mb-4">
                        <div class="flex-grow bg bg-gray-400 h-px"></div>
                        <div class="flex-grow-0 mx-5 text font-bold">Or</div>
                        <div class="flex-grow bg bg-gray-400 h-px"></div>
                    </div>
                    <div class="flex justify-between">
                        <ButtonLink
                            styling="default"
                            size="default"
                            :href="route('profile.show')">
                            Edit Profile
                        </ButtonLink>
                        <ButtonLink
                            styling="secondary"
                            size="default"
                            type="button"
                            @click="logout">
                            Log Out
                        </ButtonLink>
                    </div>
                </form>
            </div>
        </AuthenticationCard>
        <AuthBackground />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthBackground from "@/Components/AuthBackground.vue";

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <Head title="Secure Area" />
    <div class="flex flex-col lg:flex-row rounded-xl mx-auto min-h-screen">

        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo class="flex justify-center pt-8 sm:pt-0" />
            </template>
            <div class="w-96 mx-auto bg-white p-5 rounded-lg max-[500px]:w-full">
                <div class="mb-4 text-sm text-gray-600">
                    This is a secure area of the application. Please confirm your password before continuing.
                </div>
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                            class="mt-1 block w-full" required autocomplete="current-password" autofocus />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex justify-end mt-4">
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Confirm
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </AuthenticationCard>
        <AuthBackground>
            <template #imagetext>
                <p class="text-white">Lorem ipsum dolor sit amet consectetur. Adipiscing risus dignissim volutpat ut integer
                    malesuada varius fringilla. Id lacus vel lectus viverra id feugiat. Et id sed vel tincidunt amet
                    volutpat vulputate aliquet vitae. Faucibus adipiscing in dui arcu duis. Senectus semper donec dui sit
                    eget ut facilisi ut.</p>
            </template>
        </AuthBackground>
    </div>
</template>
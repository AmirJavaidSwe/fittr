<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Authpagesimages from "@/Components/Authpagesimages.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
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
            <!-- <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div> -->

            <div class="items-center justify-end mt-4">
                
                <PrimaryButton :class="{ 'opacity-25' : form.processing }" :disabled="form.processing" style="width:100%;">
                     Resend Verification Email
                </PrimaryButton>
                <div class="flex items-center mt-4 mb-4">
                            <div class="flex-grow bg bg-gray-400 h-px"></div>
                            <div class="flex-grow-0 mx-5 text font-bold">Or</div>
                            <div class="flex-grow bg bg-gray-400 h-px"></div>
                </div>
                <div class="flex rounded-md shadow-sm" role="group">
                        <Link class="justify-center w-2/4 inline-flex items-center disabled:opacity-25 transition font-semibold hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm">Edit Profile</Link>
                        <Link class="justify-center w-2/4 ml-4 text-sm text-gray-700 dark:text-gray-500 border inline-flex items-center disabled:opacity-25 transition font-semibold hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-500 hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm">Log Out</Link>
                    </div>

            </div>
        </form>
        </div>
    </AuthenticationCard>
    <Authpagesimages>
            <template #imagetext>
                <p class="text-white">Lorem ipsum dolor sit amet consectetur. Adipiscing risus dignissim volutpat ut integer malesuada varius fringilla. Id lacus vel lectus viverra id feugiat. Et id sed vel tincidunt amet volutpat vulputate aliquet vitae. Faucibus adipiscing in dui arcu duis. Senectus semper donec dui sit eget ut facilisi ut.</p>
            </template>
        </Authpagesimages>
           </div>

</template>

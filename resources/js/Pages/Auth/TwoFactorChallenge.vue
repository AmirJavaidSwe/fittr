<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthBackground from "@/Components/AuthBackground.vue";

const recovery = ref(false);
const form = useForm({
    code: '',
    recovery_code: '',
});
const recoveryCodeInput = ref(null);
const codeInput = ref(null);
const toggleRecovery = async () => {
    recovery.value ^= true;
    await nextTick();
    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};
const submit = () => {
    form.post(route('two-factor.login'));
};
</script>
<template>
    <Head title="Two-factor Confirmation" class="sm:text-center" />
    <div class="flex flex-col lg:flex-row rounded-xl mx-auto min-h-screen">
        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo class="flex justify-center pt-8 sm:pt-0" />
            </template>
            <div class="w-96 mx-auto bg-white p-5 rounded-lg max-[500px]:w-full">
                <div class="mb-4 text-sm text-gray-600">
                    <template v-if="!recovery">
                        Please confirm access to your account by entering the authentication code provided by your
                        authenticator
                        application.
                    </template>
                    <template v-else>
                        Please confirm access to your account by entering one of your emergency recovery codes.
                    </template>
                </div>

                <form @submit.prevent="submit" class="mt-7">
                    <div v-if="!recovery">
                        <InputLabel for="code" value="Code" />
                        <TextInput id="code" ref="codeInput" v-model="form.code" type="text" inputmode="numeric"
                            class="mt-1 block w-full" autofocus autocomplete="one-time-code" />
                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>
                    <div v-else>
                        <InputLabel for="recovery_code" value="Recovery Code" />
                        <TextInput id="recovery_code" ref="recoveryCodeInput" v-model="form.recovery_code" type="text"
                            class="mt-1 block w-full" autocomplete="one-time-code" />
                        <InputError class="mt-2" :message="form.errors.recovery_code" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                            @click.prevent="toggleRecovery">
                            <template v-if="!recovery">
                                Use a recovery code
                            </template>
                            <template v-else>
                                Use an authentication code
                            </template>
                        </button>
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Log in
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

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LogoLetter from '@/Components/LogoLetter.vue';
import GoogleIcon from '@/Icons/Google.vue';
import Banner from '@/Components/Banner.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import AuthBackground from "@/Components/AuthBackground.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />
    <Banner />

    <div class="min-h-screen py-30">
      <div class="mx-auto">
        <div class="flex flex-col lg:flex-row  rounded-xl mx-auto min-h-screen">
            <div class="w-full lg:w-1/2 py-16 px-12 bg-mainBg min-h-screen">
                <div class="flex justify-center pt-8 sm:pt-0">
                    <LogoLetter fill="#000000" class="w-48 h-48" />
                </div>

                <form action="#" class="w-64 xs:w-full block mx-auto max-[500px]:w-full">
                    <div class="h-px mt-4">
                        <div class="flex items-center justify-between gap-4">
                            <ButtonLink
                                styling="secondary"
                                size="default"
                                class="w-full justify-center"
                                :href="route('login')">
                                Log in
                            </ButtonLink>
                            <ButtonLink
                                styling="primary"
                                size="default"
                                class="w-full justify-center"
                                :href="route('register')">
                                Register
                            </ButtonLink>
                        </div>
                        <div class="flex items-center mt-4 mb-4">
                            <div class="flex-grow bg bg-gray-400 h-px"></div>
                            <div class="flex-grow-0 mx-5 text font-bold">Or</div>
                            <div class="flex-grow bg bg-gray-400 h-px"></div>
                        </div>
                        <a :href="route('auth.google')" class="bg-transparent hover:bg-white border border-grey flex font-medium p-4 rounded-md text-center w-full">
                            <div class="flex-grow -ml-6"><GoogleIcon class="inline-block"/> Continue with Google</div>
                        </a>
                    </div>
                </form>
            </div>
           <AuthBackground />
        </div>
      </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import LogoLetter from '@/Components/LogoLetter.vue';
import GoogleIcon from '@/Icons/Google.vue';
import Banner from '@/Components/Banner.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />
    <Banner />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-8 sm:pt-0">
                <LogoLetter fill="#000000" class="w-48 h-48" />
            </div>
            <div v-if="canLogin" class="flex flex-wrap justify-center px-6 py-4 gap-6">
                <Link v-if="$page.props.user" :href="route($page.props.user.dashboard_route)" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</Link>

                <template v-else>
                    <div>
                        <Link :href="route('login')" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</Link>
                        <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</Link>
                    </div>

                    <a :href="route('auth.google')" class="bg-white border flex font-medium p-4 rounded text-center w-full">
                        <GoogleIcon />
                        <div class="flex-grow -ml-6">Continue with Google</div>
                    </a>
                </template>
            </div>
        </div>
    </div>
</template>

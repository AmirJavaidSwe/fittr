<script setup>
import { ref, computed, defineAsyncComponent, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AppHead from '@/Layouts/AppHead.vue';
import StoreMenu from '@/Layouts/StoreMenu.vue';
import MemberMenu from '@/Layouts/MemberMenu.vue';
import InstructorMenu from '@/Layouts/InstructorMenu.vue';
import LogoLetter from '@/Components/LogoLetter.vue';
import Banner from '@/Components/Banner.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { faHeart } from '@fortawesome/free-solid-svg-icons';
import ButtonMobile from '@/Components/ButtonMobile.vue';
import ButtonLink from "@/Components/ButtonLink.vue";

import { useWindowSize } from '@/Composables/window_size.js';
import { useSwal } from '@/Composables/swal';
const { windowWidth, screen } = useWindowSize();

const props = defineProps({
    business_seetings: {
        type: Object,
        required: true,
    },
});

const mobileMenuOn = ref(false);
const toggleMobile = () => {
    mobileMenuOn.value = !mobileMenuOn.value;
};
watch(windowWidth, (newW) => {
    if(mobileMenuOn.value === true && newW >= 768){
        mobileMenuOn.value = false;
    }
});

const menuClasses = computed(() => {
    return mobileMenuOn.value
    ? 'order-last w-full bg-gray-200'
    : 'hidden md:flex';
});

const showLogin = () => {
    //add modal
    console.log('showLogin');
};
const logout = () => {
    router.post(route('logout'));
};

const LoggedinMenu = computed(() => {
    const user = usePage().props.user;
    if(!user) {
        return;
    }
    if(user.role == 'member') {
        return MemberMenu;
    }
    if(user.role == 'instructor') {
        return InstructorMenu;
    }
});

const header = computed(() => {
  return usePage().props.header;
});

const logo_image_url = computed(() => {
    return props.business_seetings.logo ? usePage().props.asset_url + props.business_seetings.logo : null;
});

const favicon_type = ref('image/x-icon');
const favicon_image_url = computed(() => {
    if(!props.business_seetings.favicon){
        return null;
    }
    switch (true) {
        case props.business_seetings.favicon.endsWith('.png'):
            favicon_type.value="image/png";
            break;
        case props.business_seetings.favicon.endsWith('.svg'):
            favicon_type.value="image/svg+xml";
            break;
    }
    return usePage().props.asset_url + props.business_seetings.favicon;
});

const headerIsArray = computed(() => {
  return Array.isArray(header.value);
})

const flash = computed(() => usePage().props.flash);
const errors = computed(() => usePage().props.errors);

const { toast } = useSwal({flash, errors});

</script>

<template>
    <div class="flex flex-col min-h-screen">
        <AppHead :title="$page.props.page_title">
            <link v-if="favicon_image_url" rel="icon" :type="favicon_type" :href="favicon_image_url" />
        </AppHead>

        <Banner />
        <!-- <FlashMessage :flash="$page.props.flash" :errors="$page.props.errors" /> -->

        <!-- Navbar -->
        <nav class="bg-primary-500">
            <div class="mx-auto max-w-7xl md:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <!-- Logo -->
                    <Link :href="business_seetings.logo_url ?? '/'" class="max-w-[140px]">
                        <div v-if="business_seetings.logo" class="h-14">
                            <img :src="logo_image_url" :alt="business_seetings.business_name" class="h-full p-1">
                        </div>
                        <div v-else class="px-6 py-4 font-bold">
                            {{business_seetings.business_name ?? 'LOGO'}}
                        </div>
                    </Link>

                    <!-- Menu -->
                    <StoreMenu class="font-medium md:font-normal text-xl text-center md:text-base gap-8 text-dark md:text-white/[.87]" :class="menuClasses" />

                    <div class="flex gap-2 items-center">
                        <!-- Mobile menu toggle -->
                        <ButtonMobile @clicked="toggleMobile" :open="mobileMenuOn" class="p-2 text-white transition block md:hidden" />

                        <!-- Settings Dropdown -->
                        <div v-if="$page.props.user" class="relative ">
                            <Dropdown align="right" width="48" :content-classes="['bg-white']">
                                <template #trigger>
                                    <button type="button" class="w-10 h-10 text-md bg-blue p-1 text-white rounded-full flex items-center justify-center text-center uppercase font-semibold">
                                        {{ $page.props.user.initials }}
                                        <!-- <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name"> -->
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="px-4 py-2 border-b bg-gray-50">
                                        <div class="font-bold">{{$page.props.user.name}}</div>
                                        <div class="text-gray-400">{{$page.props.user.email}}</div>
                                    </div>

                                    <DropdownLink :href="route('profile.show')">
                                        Profile
                                    </DropdownLink>

                                    <div class="border-t border-gray-100" />

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button">
                                            Log Out
                                        </DropdownLink>
                                    </form>
                                </template>
                            </Dropdown>
                        </div>
                        <template v-else>
                            <ButtonLink :href="route('login')" styling="secondary" size="default" @click="showLogin" class="hidden md:inline-flex">Login</ButtonLink>
                            <ButtonLink :href="route('register')" styling="secondary" size="default" @click="showLogin" class="hidden md:inline-flex">Sign up</ButtonLink>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <div class="bg-gray-50 md:flex md:flex-grow bg-mainBg">
            <component :is="LoggedinMenu" />
            <!-- Page Content -->
            <main class="flex flex-col flex-grow py-8 sm:px-6 lg:px-8 space-y-8">
                <div>Window width: {{ windowWidth }} screen: {{ screen }}</div>
                <slot />
            </main>
        </div>
        <!-- footer -->
        <footer class="bg-primary-600 p-2 text-center text-primary-100 text-xs">MADE WITH <font-awesome-icon :icon="faHeart" class="mx-2" /> IN LONDON</footer>
    </div>
</template>

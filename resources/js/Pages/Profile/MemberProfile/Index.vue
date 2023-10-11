<script setup>
import { ref } from 'vue';
import ProfileMenu from "./ProfileMenu.vue";
import ProfileInfo from "./ProfileInfo.vue";
import ProfilePersonalDetails from "./ProfilePersonalDetails.vue";
import Section from "@/Components/Section.vue";
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    business_settings: Object,
    user: Object
});


</script>

<template>
    <Section bg="bg-transparent" class="lg:p-8">
        <ProfileMenu />
        <div class="flex flex-col md:flex-row justify-between mt-12">
            <ProfileInfo :user="props.user" :business_settings="business_settings" />
            <ProfilePersonalDetails :user="props.user" :business_settings="business_settings" />
        </div>
    </Section>
</template>

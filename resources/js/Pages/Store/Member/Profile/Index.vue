<script setup>
import { ref } from 'vue';
import ProfileMenu from "./../../../Profile/MemberProfile/ProfileMenu.vue";
import ProfileInfo from "./../../../Profile/MemberProfile/ProfileInfo.vue";
import ProfilePersonalDetails from "./../../../Profile/MemberProfile/ProfilePersonalDetails.vue";
import Section from "@/Components/Section.vue";
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    business_settings: Object,
    member: Object
});


</script>

<template>
    <Section bg="bg-transparent" class="lg:p-8">
        <ProfileMenu />
        <div class="flex flex-col md:flex-row justify-between mt-12">
            <ProfileInfo :user="member" :business_settings="business_settings" />
            <ProfilePersonalDetails :user="member" :business_settings="business_settings" />
        </div>
    </Section>
</template>

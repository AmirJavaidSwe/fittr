<script setup>
import Section from "@/Components/Section.vue";
import Package from "@/Components/Package.vue";
import CardBasic from "@/Components/CardBasic.vue";
import CardIcon from "@/Components/CardIcon.vue";
import WarningButton from "@/Components/WarningButton.vue";
import {
    faBookOpen,
    faUsers,
    faUserTie,
} from "@fortawesome/free-solid-svg-icons";
const props = defineProps({
    partner: Object,
    totalClasses: {
        type: Number,
        required: true,
    },
    totalMembers: {
        type: Number,
        required: true,
    },
    totalInstructors: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl lg:text-24vw">Hello, {{ partner.name }}</div>
        <hr class="mb-4" />
        <div></div>

        <CardBasic v-if="partner.active_subscription">
            <template #header> Your service package is active </template>

            <template #footer>
                <WarningButton
                    :href="route('partner.subscriptions.index')"
                    type="primary"
                    >Manage</WarningButton
                >
            </template>
        </CardBasic>
        <CardBasic v-else>
            <template #header> You are not subscribed to any package </template>

            <template #footer>
                <WarningButton
                    :href="route('partner.subscriptions.index')"
                    type="primary"
                    >Select package</WarningButton
                >
            </template>
        </CardBasic>

        <div class="bg-white flex flex-wrap gap-8 my-4 p-4 rounded-lg">
            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faBookOpen" />
                </template>

                <template #title>
                    {{ props.totalClasses }}
                </template>

                <template #default> Total Classes </template>
            </CardIcon>

            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faUsers" />
                </template>

                <template #title>
                    {{ props.totalMembers }}
                </template>

                <template #default> Total Members </template>
            </CardIcon>

            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faUserTie" />
                </template>

                <template #title>
                    {{ props.totalInstructors }}
                </template>

                <template #default> Total Instructors </template>
            </CardIcon>
        </div>
    </Section>
</template>

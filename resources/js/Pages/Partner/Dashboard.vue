<script setup>
import Section from "@/Components/Section.vue";
import Package from "@/Components/Package.vue";
import CardBasic from "@/Components/CardBasic.vue";
import CardIcon from "@/Components/CardIcon.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
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

const showAlert = () => {
    alert("button clicked");
};
const showDemoOnly = false
</script>

<template>
    <Section bg="bg-transparent">
        <!-- buttons demo -->
        <div class="space-y-4 mb-8" v-if="showDemoOnly">
            <p>Demo only</p>
            <h2 class="font-bold text-lg">
                @\Components\ButtonLink.vue - single component to replace all
                buttons:
            </h2>
            <p>
                This is a
                <ButtonLink :href="route('partner.dashboard')">link</ButtonLink>
                inside paragraph.
            </p>
            <div>
                This is a
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="blank"
                    as="button"
                    >blank button</ButtonLink
                >
                with href, GET method
            </div>
            <div>
                This is another blank button
                <ButtonLink
                    :href="route('logout')"
                    styling="blank"
                    as="button"
                    method="post"
                    >logout</ButtonLink
                >
                with href, POST method
            </div>
            <div>
                This is blank button with sizes (padding):
                <ButtonLink href="#" styling="blank" size="small"
                    >Small button</ButtonLink
                >
                <ButtonLink href="#" styling="blank" size="default"
                    >Default button</ButtonLink
                >
                <ButtonLink href="#" styling="blank" size="medium"
                    >Medium button</ButtonLink
                >
                <ButtonLink href="#" styling="blank" size="large"
                    >Large button</ButtonLink
                >
            </div>
            <div class="flex flex-wrap items-start gap-4">
                <span class="flex-grow"
                    >This is a primary &lt;button&gt; with link:
                </span>
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="primary"
                    size="small"
                    as="button"
                    >Small button</ButtonLink
                >
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="primary"
                    size="default"
                    as="button"
                    >Default button</ButtonLink
                >
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="primary"
                    size="medium"
                    as="button"
                    >Medium button</ButtonLink
                >
                <ButtonLink styling="primary" size="large" @click="showAlert"
                    >Large button, no href, v-on</ButtonLink
                >
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <span class="flex-grow"
                    >This is a default button as &lt;a&gt; link:
                </span>
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="default"
                    size="small"
                    >Small button</ButtonLink
                >
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="default"
                    size="default"
                    >Default button</ButtonLink
                >
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="default"
                    size="medium"
                    >Medium button</ButtonLink
                >
                <ButtonLink styling="default" size="large" @click="showAlert"
                    >Large button, no href, v-on</ButtonLink
                >
            </div>
            <div class="flex flex-wrap items-end gap-4">
                <span class="flex-grow"
                    >This is a secondary button as &lt;a&gt; link:
                </span>
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="secondary"
                    size="small"
                    >Small button</ButtonLink
                >
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="secondary"
                    size="default"
                    >Default button</ButtonLink
                >
                <ButtonLink
                    :href="route('partner.dashboard')"
                    styling="secondary"
                    size="medium"
                    >Medium button</ButtonLink
                >
                <ButtonLink styling="secondary" size="large" @click="showAlert"
                    >Large button, no href, v-on</ButtonLink
                >
            </div>
        </div>

        <div class="md:textlg xl:text-xl">Hello, {{ partner.name }}</div>
        <hr class="mb-4" />
        <div></div>

        <CardBasic v-if="partner.active_subscription">
            <template #header> Your service package is active </template>

            <template #footer>
                <ButtonLink
                    styling="secondary"
                    size="default"
                    :href="route('partner.subscriptions.index')"
                    type="primary"
                    >Manage</ButtonLink
                >
            </template>
        </CardBasic>
        <CardBasic v-else>
            <template #header> You are not subscribed to any package </template>

            <template #footer>
                <ButtonLink
                    styling="secondary"
                    size="default"
                    :href="route('partner.subscriptions.index')"
                >
                    Select package
                </ButtonLink>
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

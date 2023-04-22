<script setup>
import Section from '@/Components/Section.vue';
import Package from '@/Components/Package.vue';
import CardBasic from '@/Components/CardBasic.vue';
import CardIcon from '@/Components/CardIcon.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { 
    faBookOpen,
    faUsers,
    faUserTie
} from '@fortawesome/free-solid-svg-icons';
const props = defineProps({
    subdomain: Object,
    totalClasses: {
        type: Number,
        required: true,
    },
    totalInstructors: {
        type: Number,
        required: true,
    }
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl">
            Hello, this is service store of <b>https://{{subdomain.val}}.{{$page.props.app_domain}}</b>
        </div>
        <hr class="mb-4">
        <div></div>

        <CardBasic>
            <template #header>
                service store is active
            </template>

            <template #footer>
                <ButtonLink href="#" type="primary">Manage bookings</ButtonLink>
            </template>
        </CardBasic>

        <div class="bg-white flex gap-8 my-4 p-4 rounded-lg">
            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faBookOpen" />
                </template>

                <template #title>
                    {{props.totalClasses}}
                </template>

                <template #default>
                    Total Classes
                </template>
            </CardIcon>

            <CardIcon>
                <template #icon>
                    <font-awesome-icon :icon="faUserTie" />
                </template>

                <template #title>
                    {{props.totalInstructors}}
                </template>

                <template #default>
                    Total Instructors
                </template>
            </CardIcon>
        </div>

    </Section>
</template>

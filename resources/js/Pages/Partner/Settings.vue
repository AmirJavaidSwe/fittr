<script setup>
import { computed } from 'vue';
import { 
    faMoneyBill1,
    faGears,
    faCodeCompare,
    faStore,
    faCode,
    faFileContract,
    faTicket,
    faCreditCard,
    faFileInvoice,
    faPeopleGroup,
    faEnvelopesBulk,
    faDoorOpen,
    faGamepad,
} from '@fortawesome/free-solid-svg-icons';
import Section from '@/Components/Section.vue';
import CardIcon from '@/Components/CardIcon.vue';
</script>

<template>
    <Section bg="bg-white">
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Business Settings
                </div>

                <CardIcon :card-link="route('partner.settings.general-details')">
                    <template #icon>
                        <font-awesome-icon :icon="faGears" />
                    </template>

                    <template #title>
                        General
                    </template>

                    <template #default>
                        Business and operations settings
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('root')">
                    <template #icon>
                        <font-awesome-icon :icon="faMoneyBill1" />
                    </template>

                    <template #title>
                        Taxes & Charges
                    </template>

                    <template #default>
                        Add taxes for your services
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.settings.integrations')">
                    <template #icon>
                        <font-awesome-icon :icon="faCodeCompare" />
                    </template>

                    <template #title>
                        Integrations
                    </template>

                    <template #default>
                        Connect external apps to your business
                    </template>
                </CardIcon>
            </div>
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Studios
                </div>

                <CardIcon :card-link="route('partner.studios.index')">
                    <template #icon>
                        <font-awesome-icon :icon="faDoorOpen" />
                    </template>

                    <template #title>
                        Studios
                    </template>

                    <template #default>
                        Manage studios
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.classtypes.index')">
                    <template #icon>
                        <font-awesome-icon :icon="faGamepad" />
                    </template>

                    <template #title>
                        Class Types
                    </template>

                    <template #default>
                        Manage studio class types
                    </template>
                </CardIcon>

            </div>
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Online Store
                </div>

                <CardIcon :card-link="route('partner.settings.service-store-general')">
                    <template #icon>
                        <font-awesome-icon :icon="faStore" />
                    </template>

                    <template #title>
                        Service store
                    </template>

                    <template #default>
                        Customise web store looks and features
                    </template>
                </CardIcon>     

                <CardIcon card-link="#">
                    <template #icon>
                        <font-awesome-icon :icon="faCode" />
                    </template>

                    <template #title>
                        Widgets
                    </template>

                    <template #default>
                        Manage service store widgets for your websites
                    </template>
                </CardIcon>
                <CardIcon :card-link="route('partner.settings.service-store-waivers')">
                    <template #icon>
                        <font-awesome-icon :icon="faFileContract" />
                    </template>

                    <template #title>
                        Waivers
                    </template>

                    <template #default>
                        Create waivers to your members
                    </template>
                </CardIcon>
            </div>
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Bookings & Payments
                </div>

                <CardIcon card-link="#">
                    <template #icon>
                        <font-awesome-icon :icon="faTicket" />
                    </template>

                    <template #title>
                        Booking Flow
                    </template>

                    <template #default>
                        Manage Preferences
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.settings.payments')">
                    <template #icon>
                        <font-awesome-icon :icon="faCreditCard" />
                    </template>

                    <template #title>
                        Payments
                    </template>

                    <template #default>
                        Setup business payments
                    </template>
                </CardIcon>
                <CardIcon card-link="#">
                    <template #icon>
                        <font-awesome-icon :icon="faFileInvoice" />
                    </template>

                    <template #title>
                        Invoices
                    </template>

                    <template #default>
                        Customise generated invoices
                    </template>
                </CardIcon>
            </div>
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Team & Communication
                </div>

                <CardIcon card-link="#">
                    <template #icon>
                        <font-awesome-icon :icon="faPeopleGroup" />
                    </template>

                    <template #title>
                        Team Access
                    </template>

                    <template #default>
                        Manage your staff and permissions
                    </template>
                </CardIcon>     

                <CardIcon card-link="#">
                    <template #icon>
                        <font-awesome-icon :icon="faEnvelopesBulk" />
                    </template>

                    <template #title>
                        Notifications
                    </template>

                    <template #default>
                        Manage system generated emails
                    </template>
                </CardIcon>
            </div>
        </div>
    </Section>
</template>

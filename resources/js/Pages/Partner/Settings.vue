<script setup>
import { computed } from 'vue';
import {
    // faMoneyBill1,
    faGears,
    // faCodeCompare,
    // faStore,
    // faCode,
    // faFileContract,
    faTicket,
    // faTicketSimple,
    // faCreditCard,
    // faFileInvoice,
    // faShieldHalved,
    // faPeopleGroup,
    // faEnvelopesBulk,
    // faDoorOpen,
    // faGamepad,
    // faWandMagicSparkles,
    faLocationPinLock,
} from '@fortawesome/free-solid-svg-icons';
import Section from '@/Components/Section.vue';
import CardIcon from '@/Components/CardIcon.vue';
import ServicesStoreIcon from "@/Icons/Store.vue";
// import BookingIcon from "@/Icons/Booking.vue";
import TeamIcon from "@/Icons/Team.vue";
import WidgetIcon from "@/Icons/Widget.vue";
import PaymentIcon from "@/Icons/Payment.vue";
import RolesIcon from "@/Icons/Shield.vue";
import WaviersIcon from "@/Icons/Waviers.vue";
import InvoicesIcon from "@/Icons/Invoices.vue";
import NotificationsIcon from "@/Icons/Bell.vue";
import LocationIcon from "@/Icons/Location.vue";
import PackIcon from "@/Icons/Pack.vue";
//import StudiosIcon from "@/Icons/Studio.vue";
import TaxesandChargesIcon from "@/Icons/Tax.vue";
import ClassTypesIcon from "@/Icons/ClassType.vue";
import IntegerationIcon from "@/Icons/Integeration.vue";
import AmenityIcon from "@/Icons/Amenity.vue";
import BookingIcon from "@/Icons/BookingIcon.vue";
import StudiosIcon from "@/Icons/StudiosIcon.vue";
import GeneralIcon from "@/Icons/GeneralIcon.vue";
</script>

<template>
    <Section bg="bg-white">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Online Store
                </div>

                <CardIcon :card-link="route('partner.settings.service-store-general')">
                    <template #icon>
                        <ServicesStoreIcon />
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
                        <WidgetIcon />
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
                        <WaviersIcon />
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
                        <!-- <BookingIcon /> -->
                        <BookingIcon />
                    </template>

                    <template #title>
                        Booking
                    </template>

                    <template #default>
                        Manage Preferences
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.settings.payments')">
                    <template #icon>
                        <PaymentIcon/>
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
                        <InvoicesIcon />
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

                <CardIcon :card-link="route('partner.users.index')">
                    <template #icon>
                        <TeamIcon />
                    </template>

                    <template #title>
                        Team
                    </template>

                    <template #default>
                        Manage organization users
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.roles.index')">
                    <template #icon>
                        <RolesIcon />
                    </template>

                    <template #title>
                        Roles
                    </template>

                    <template #default>
                        Manage roles and permissions
                    </template>
                </CardIcon>

                <CardIcon card-link="#">
                    <template #icon>
                        <NotificationsIcon />
                    </template>

                    <template #title>
                        Notifications
                    </template>

                    <template #default>
                        Manage system generated emails
                    </template>
                </CardIcon>
            </div>
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Locations & Studios
                </div>

                <CardIcon :card-link="route('partner.locations.index')">
                    <template #icon>
                        <LocationIcon />
                    </template>

                    <template #title>
                        Locations
                    </template>

                    <template #default>
                        Manage locations
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.studios.index')">
                    <template #icon>
                        <StudiosIcon />
                    </template>

                    <template #title>
                        Studios
                    </template>

                    <template #default>
                        Manage studios
                    </template>
                </CardIcon>

                <CardIcon
                    :card-link="route('partner.classtypes.index')"
                    v-can="{ module: 'studio-class-type', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }"
                    >
                    <template #icon>
                        <ClassTypesIcon />
                    </template>

                    <template #title>
                        Class Types
                    </template>

                    <template #default>
                        Manage studio class types
                    </template>
                </CardIcon>

                <CardIcon
                    :card-link="route('partner.amenity.index')"
                    v-can="{ module: 'studio-amenities', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }"
                    >
                    <template #icon>
                        <AmenityIcon />
                    </template>

                    <template #title>
                        Amenities
                    </template>

                    <template #default>
                        Manage amenities
                    </template>
                </CardIcon>

            </div>
            <div class="space-y-4">
                <div class="text-xl font-bold tracking-tight text-gray-900">
                    Business Settings
                </div>

                <CardIcon :card-link="route('partner.settings.general-details')">
                    <template #icon>
                        <GeneralIcon />
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
                        <TaxesandChargesIcon />
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
                        <IntegerationIcon />
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
                    Memberships
                </div>

                <CardIcon :card-link="route('partner.packs.index')">
                    <template #icon>
                        <PackIcon />
                    </template>

                    <template #title>
                        Pack
                    </template>

                    <template #default>
                        Manage class packs and memberships
                    </template>
                </CardIcon>

                <CardIcon :card-link="route('partner.settings.fap')">
                    <template #icon>
                        <font-awesome-icon :icon="faLocationPinLock" class="text-primary-500 h-8 w-8" />
                    </template>

                    <template #title>
                        Fair Access Policy
                    </template>

                    <template #default>
                        Unlimited packs FAP
                    </template>
                </CardIcon>

            </div>
            
        </div>
    </Section>
</template>

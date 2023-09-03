<script setup>
import { ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import {
    faPencil,
    faChevronRight,
    faPlus,
    faEye,
    faCog,
} from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
import MembershipCard from "@/Pages/Store/Member/Memberships/MembershipCard.vue";

const props = defineProps({
    memberships: Object,
    locations: Array,
    pack_types: Array,
    // price_types: Array,
    classtypes: Array,
    servicetypes: Array,
    locations: Array,
    business_settings: Object,
});

</script>
<template>
    <div class="space-y-8">
        <MembershipCard
            v-for="membership in memberships" :key="membership.id"
            :membership="membership"
            :pack_types="pack_types"
            :classtypes="classtypes"
            :servicetypes="servicetypes"
            :locations="locations"
            :business_settings="business_settings"
            class="bg-white border-t-8 rounded-md shadow"
        />
    </div>
</template>

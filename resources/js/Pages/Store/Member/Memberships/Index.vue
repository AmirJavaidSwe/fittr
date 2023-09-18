<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
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

const tryCancel = (membership) => {
    router.put(route('ss.member.memberships.cancel', { subdomain: props.business_settings.subdomain, membership: membership.id } ));
};

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
            @cancel="tryCancel"
            class="bg-white border-t-8 rounded-md shadow"
        />
    </div>
</template>

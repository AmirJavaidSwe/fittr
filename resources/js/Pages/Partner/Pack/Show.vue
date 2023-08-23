<script setup>
import { computed } from 'vue';
import {Link} from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

const props = defineProps({
    pack: {
        type: Object,
        required: true,
    },
    pack_types: Object,
    periods: Object,
    price_types: Object,
    business_settings: Object,
})
const isDefaultType = computed(() => {
  return props.pack.type == 'default';
});
</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <Link
                    class="cursor-pointer h-10 inline-flex items-center justify-center rounded-md border border-transparent
                            bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none
                            focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                    :href="route('partner.packs.edit', pack)">
                    Edit
                </Link>
            </div>
        </template>
        <template #item>
            <single-view-row label="Type" :value="pack_types.find(element => element.value == pack.type).label"/>
            <single-view-row label="ID" :value="pack.id"/>
            <single-view-row label="Title" :value="pack.title"/>
            <single-view-row label="Sub title" :value="pack.sub_title"/>
            <single-view-row label="Description" :value="pack.description"/>
            <single-view-row label="Product ID" :value="pack.stripe_product_id"/>
            <single-view-row label="Pack is active" :value="pack.is_active ? 'Yes':'no'"/>
            <single-view-row label="Price options">
                <template v-slot:value v-if="pack.prices">
                    <div v-for="price in pack.prices" class="shadow-md w-80">
                        <div class="flex font-medium items-center justify-between space-between p-2" :class="price.is_active ? 'bg-green-400' : 'bg-gray-400'">
                            <div>{{price_types.find(element => element.value == price.type).label }}</div>
                            <div>{{price.is_active ? 'Active':'Inactive'}}</div>
                        </div>
                        <div class="p-2">
                            <div>ID: {{price.id}}</div>
                            <div class="font-bold">{{price.price}} {{price.currency.toUpperCase()}}</div>
                            <div v-if="!isDefaultType" class="font-bold">Session credits: {{price.sessions}}</div>
                            <div v-if="!isDefaultType">Session credits: {{price.is_expiring ? 'will' : 'never'}} expire</div>
                            <div v-if="price.is_expiring">Expiration value/period: {{price.expiration}} / {{price.expiration_period}}</div>
                            <div v-if="price.type == 'one_time'">{{'Pack is ' + (price.is_renewable ? '' : 'not ') + 'renewable'}}</div>
                            <div v-if="price.type == 'one_time'">{{'Pack is ' + (price.is_intro ? 'intro' : 'not intro')}}</div>
                            <div v-if="price.type == 'recurring'">Billing interval: every {{price.interval_count}} {{price.interval}}</div>
                            <div v-if="price.type == 'recurring'">Subscription duration: {{price.is_ongoing ? 'runs indefinitely' : 'fixed cycles'}}</div>
                            <div v-if="price.type == 'recurring' && price.is_ongoing === false">Stops after: {{price.fixed_count}} billing cycles</div>
                        </div>
                    </div>
                </template>
            </single-view-row>
            
            <single-view-row label="Intro pack" :value="pack.is_intro ? 'Yes':'No'"/>
            <single-view-row label="Unlimited pack" :value="pack.is_unlimited ? 'Yes':'No'"/>
            <single-view-row label="Use Fair Access Policy" :value="pack.is_fap ? 'Yes':'No'"/>
            <single-view-row label="Has restrictions" :value="pack.is_restricted ? 'Yes':'No'"/>
            <single-view-row label="Private pack" :value="pack.is_private ? 'Yes':'No'"/>
            <single-view-row label="Private URL" :value="pack.private_url"/>
            <single-view-row label="Pack active from" :value="pack.active_from ? DateTime.fromISO(pack.active_from).setZone(business_settings.timezone).toFormat(business_settings.date_format.format_js) : null"/>
            <single-view-row label="Pack active to" :value="pack.active_to ? DateTime.fromISO(pack.active_to).setZone(business_settings.timezone).toFormat(business_settings.date_format.format_js) : null"/>
            <single-view-row label="Created At" :value="DateTime.fromISO(pack.created_at).setZone(business_settings.timezone).toFormat(business_settings.date_format.format_js)"/>
            <single-view-row label="Updated At" :value="DateTime.fromISO(pack.updated_at).toRelative()"/>
        </template>
    </single-view>
</template>

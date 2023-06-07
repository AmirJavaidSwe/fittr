<script setup>
import Section from '@/Components/Section.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
const props = defineProps({
    packs: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">
            Memberships
        </div>
        <div class="flex flex-wrap gap-4 flex-1">
            <div v-for="item in packs" :key="item.id" class="bg-white rounded shadow-md overflow-hidden w-96">
                <div class="font-bold bg-green-700 text-white p-4">{{item.title}} type: {{item.type}}</div>
                <div class="p-4">
                    <div v-if="item.prices.length" class="py-2 border-y-2">
                        <div v-for="price in item.prices" class="flex justify-between items-center mb-4 last:mb-0">
                            <div><span>{{price.currency}}</span> <span class="font-bold text-3xl">{{price.price}}</span></div>
                            <!-- <br> -->
                            <!-- Active: {{price.is_active ? 'Yes': 'No'}} -->
                            <PrimaryButton 
                                v-if="price.type == 'one_time'"
                                type="button"
                                :disabled="!price.is_active"
                                >Buy</PrimaryButton>
                            <PrimaryButton 
                                v-if="price.type == 'recurring'"
                                type="button"
                                :disabled="!price.is_active"
                                >Subscribe</PrimaryButton>
                        </div>
                    </div>
                    <div v-else class="py-2 border-y-2 text-center">Not available for purchase</div>

                    <div>sub_title: {{item.sub_title}}</div>
                    <div>description: {{item.description}}</div>

                    <div>is_active: {{item.is_active}}</div>
                    <div>is_restricted: {{item.is_restricted}}</div>
                    <div>is_unlimited: {{item.is_unlimited}}</div>
                    <div>is_fap: {{item.is_fap}}</div>
                    <div>restrictions: {{item.restrictions}}</div>
                </div>
            </div>
        </div>
    </Section>
</template>

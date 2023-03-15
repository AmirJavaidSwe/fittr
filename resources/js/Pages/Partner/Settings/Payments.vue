<script setup>
import Section from '@/Components/Section.vue';
import CardIcon from '@/Components/CardIcon.vue';
import StripeIcon from '@/Icons/Stripe.vue';
</script>

<template>
     <Section bg="bg-white space-y-4">
        <div class="text-xl font-bold tracking-tight text-gray-900">
            Payment gateways
        </div>
        <CardIcon :card-link="route('partner.settings.payments.stripe')">
            <template #icon>
                <div class="w-24">
                    <StripeIcon />
                </div>
            </template>

            <template #title>
                Stripe
            </template>

            <template #default>
                The best payment gateway globally. Build and scale a recurring business model with Stripe.
            </template>
        </CardIcon>
    </Section>
</template>

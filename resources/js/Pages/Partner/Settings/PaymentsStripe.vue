<script setup>
import { useForm } from "@inertiajs/vue3";
import Section from "@/Components/Section.vue";
import CardIcon from "@/Components/CardIcon.vue";
import StripeIcon from "@/Icons/Stripe.vue";
import VisaIcon from "@/Icons/Visa.vue";
import MastercardIcon from "@/Icons/Mastercard.vue";
import AmexIcon from "@/Icons/Amex.vue";
import DiscoverIcon from "@/Icons/Discover.vue";
import DinersclubIcon from "@/Icons/Dinersclub.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    has_account: Boolean,
    stripe_account: Object,
});
const form = useForm({});
const submitForm = () => {
    form.post(route("partner.settings.payments.stripe"));
};
</script>

<template>
    <Section bg="bg-white space-y-8">
        <div class="text-xl font-bold tracking-tight text-gray-900">Stripe</div>
        <div>
            <p>With Stripe,</p>
            <ul>
                <li>
                    Accept payments for classes, products, add-ons and anything
                    you want to sell as goods or services
                </li>
                <li>Accept recurring payments for subscriptions</li>
                <li>Charge late cancellation fees</li>
                <li>Charge no-show fees</li>
            </ul>
        </div>
        <div class="text-xl font-bold tracking-tight text-gray-900">
            Accepted international cards
        </div>
        <div class="flex flex-wrap gap-8">
            <CardIcon>
                <template #icon>
                    <div class="w-12">
                        <VisaIcon />
                    </div>
                </template>

                <template #title> Visa </template>

                <template #default> </template>
            </CardIcon>
            <CardIcon>
                <template #icon>
                    <div class="w-12">
                        <MastercardIcon />
                    </div>
                </template>

                <template #title> Mastercard </template>
            </CardIcon>
            <CardIcon>
                <template #icon>
                    <div class="w-12">
                        <AmexIcon />
                    </div>
                </template>

                <template #title> American Express </template>
            </CardIcon>
            <CardIcon>
                <template #icon>
                    <div class="w-12">
                        <DiscoverIcon />
                    </div>
                </template>

                <template #title> Discover </template>
            </CardIcon>
            <CardIcon>
                <template #icon>
                    <div class="w-12">
                        <DinersclubIcon />
                    </div>
                </template>

                <template #title> Diners Club </template>
            </CardIcon>
        </div>

        <div v-if="has_account" class="font-bold">
            Your stripe account created
        </div>
        <div
            v-if="stripe_account?.charges_enabled"
            class="text-green-600 p-4 bg-green-100"
        >
            Account is ready to accept payments
        </div>

        <form @submit.prevent="submitForm">
            <ButtonLink
                styling="secondary"
                size="default"
                v-if="!has_account"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                type="submit"
            >
                Connect Stripe account
            </ButtonLink>
            <ButtonLink
                styling="default"
                size="default"
                v-if="has_account"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                type="submit"
            >
                Update details
            </ButtonLink>
        </form>

        <!-- temp -->
        <div v-if="stripe_account" class="bg-gray-100 p-4">
            <ul>
                <li v-for="(value, key) in stripe_account">
                    <b>{{ key }}</b
                    >: {{ value }}
                </li>
            </ul>
        </div>
    </Section>
</template>

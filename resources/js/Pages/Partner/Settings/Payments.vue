<script setup>
import StipesIcon from "@/Icons/StipesIcon.vue";
import CheckIcon from "@/Icons/CheckIcon.vue";
import DoubleArrow from "@/Icons/DoubleArrow.vue";
import VisaNew from "@/Icons/VisaNew.vue";
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
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { faCog } from "@fortawesome/free-solid-svg-icons";
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
    <Section bg="bg-white space-y-4">
        <div>
            <div
                class="bg-white rounded bg-white bg-white-50 hover:bg-white-100 items-center p-3 pt-5 gap-4 relative transition rounded">
                <h3 class="text-2xl"><strong>Stripe Connect</strong></h3>
                <p class="text-base">
                    Connect your Stripe merchant account to let Appointment thing charge your customers as appointments are
                    scheduled.
                </p>
                <div class="accepted mb-5 mt-5" v-if="has_account">
                    <p class="mb-2 text-base">Accepted international cards</p>
                    <VisaNew />
                </div>
                <div class="bg-gray-100 p-5 rounded mt-10">
                    <StipesIcon />
                    <div class="block h-px mt-4 mb-4 bg-gray-200 border-0 dark:bg-gray-700"></div>
                    <div class="flex flex-col">
                        <h3 class="text-lg">
                            <strong>Connect your Stripe Account.</strong>
                        </h3>
                        <p class="text-gray-400 mt-2">
                            <span class="mr-2 border-l-4 border-[#315D3F] rounded-md border-t-[4px]">
                            </span> Allows your to accept Stripe Payments for appointments.
                        <form @submit.prevent="submitForm" class="inline-flex float-right items-center">
                            <ButtonLink styling="secondary" size="default" v-if="!has_account"
                                :class="['float-right', 'opacity-25' && form.processing]" :disabled="form.processing"
                                type="submit">
                                <DoubleArrow /> &nbsp; Connect
                            </ButtonLink>
                            <ButtonLink size="default" v-if="has_account"
                                :class="['float-right', 'opacity-25' && form.processing]" :disabled="form.processing"
                                type="button">
                                <CheckIcon /> &nbsp; <span class="text-[#36B07E]">Connected</span>
                            </ButtonLink>
                            <Dropdown v-if="has_account" @click.prevent class="ms-4" align="right" width="48" :content-classes="['bg-white']">
                                <template #trigger>
                                    <button class="text-dark text-lg">
                                        <font-awesome-icon :icon="faCog" />
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink as="button" @click="submitForm">
                                        <span class="text-primary-500 flex items-center">
                                            <DoubleArrow /> &nbsp; <span>Update Details</span>
                                        </span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </form>
                        </p>
                    </div>
                </div>
                <div class="mt-2 bg-white rounded pt-5 flex gap-4 md:flex-col lg:flex-row sm:flex-col" v-if="has_account">
                    <div class="bg-gray-100 p-5 rounded">
                        <h2 class="flex justify-between items-center text-slate-400"><strong>PAYMENTS</strong>
                            <StipesIcon />
                        </h2>
                        <div class="flex">
                            <div class="relative">
                                <span class="flex items-end">
                                    <svg width="8" height="11" viewBox="0 0 8 11" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M0.451172 9.30371C0.779297 9.12598 1.11882 8.84115 1.46973 8.44922C1.82064 8.05273 1.99609 7.66536 1.99609 7.28711C1.99609 7.08659 1.95964 6.85872 1.88672 6.60352C1.8457 6.45768 1.77734 6.26172 1.68164 6.01562H0.464844V5.25H1.24414C1.03451 4.83984 0.879557 4.47982 0.779297 4.16992C0.679036 3.85547 0.628906 3.51823 0.628906 3.1582C0.628906 2.45638 0.918294 1.82747 1.49707 1.27148C2.0804 0.715495 2.90983 0.4375 3.98535 0.4375C4.87858 0.4375 5.65332 0.685872 6.30957 1.18262C6.96582 1.6748 7.31445 2.50879 7.35547 3.68457H5.50293C5.45736 3.2334 5.35254 2.86882 5.18848 2.59082C4.94694 2.20345 4.53906 2.00977 3.96484 2.00977C3.54102 2.00977 3.20605 2.14193 2.95996 2.40625C2.71387 2.66602 2.59082 2.9668 2.59082 3.30859C2.59082 3.51367 2.63411 3.73014 2.7207 3.95801C2.80729 4.18132 3.00098 4.61198 3.30176 5.25H5.26367V6.01562H3.5957C3.61393 6.19336 3.6276 6.34147 3.63672 6.45996C3.65039 6.57845 3.65723 6.69694 3.65723 6.81543C3.65723 7.25293 3.52507 7.65853 3.26074 8.03223C3.09212 8.26465 2.75033 8.61784 2.23535 9.0918C2.59993 8.95964 2.86882 8.87305 3.04199 8.83203C3.31999 8.75911 3.59115 8.72266 3.85547 8.72266C4.01953 8.72266 4.17904 8.73633 4.33398 8.76367C4.49349 8.79102 4.66211 8.82975 4.83984 8.87988L5.12012 8.96875C5.24316 9.00977 5.3457 9.03711 5.42773 9.05078C5.51432 9.0599 5.58724 9.06445 5.64648 9.06445C5.85612 9.06445 6.10221 9.00521 6.38477 8.88672C6.54883 8.81836 6.75391 8.71354 7 8.57227L7.56055 10.0967C7.33268 10.2471 7.01367 10.3929 6.60352 10.5342C6.19336 10.6755 5.79915 10.7461 5.4209 10.7461C5.22038 10.7461 5.01074 10.721 4.79199 10.6709C4.57324 10.6208 4.33626 10.5547 4.08105 10.4727L3.69141 10.3428C3.52279 10.2881 3.38379 10.2471 3.27441 10.2197C3.09212 10.1833 2.90527 10.165 2.71387 10.165C2.50879 10.165 2.28776 10.2061 2.05078 10.2881C1.89583 10.3473 1.611 10.4727 1.19629 10.6641L0.451172 9.30371Z"
                                            fill="#292D32" fill-opacity="0.5" />
                                    </svg>
                                    <strong class="text-4xl text-slate-800">524.415£</strong>
                                </span>
                            </div>
                            <div class="flex flex-col ml-4">
                                <span class="flex">13%
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.89645 11.6464C1.70118 11.8417 1.70118 12.1583 1.89645 12.3536C2.09171 12.5488 2.40829 12.5488 2.60355 12.3536L1.89645 11.6464ZM6.58929 7.66071L6.94284 7.30716L6.58929 6.95361L6.23573 7.30716L6.58929 7.66071ZM9.96429 11.0357L9.61073 11.3893L9.96429 11.7428L10.3178 11.3893L9.96429 11.0357ZM15.75 5.25L10.1732 6.74429L14.2557 10.8268L15.75 5.25ZM2.60355 12.3536L6.94284 8.01427L6.23573 7.30716L1.89645 11.6464L2.60355 12.3536ZM6.23573 8.01427L9.61073 11.3893L10.3178 10.6822L6.94284 7.30716L6.23573 8.01427ZM10.3178 11.3893L12.9216 8.78553L12.2145 8.07843L9.61073 10.6822L10.3178 11.3893Z"
                                            fill="#36B07E" />
                                    </svg>
                                </span>
                                <span>vs last 7 days</span>
                            </div>
                            <div class="flex flex-col ml-4">
                                <span>
                                    <svg width="100%" height="44" viewBox="0 0 156 44" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="102.738" cy="20" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="78.1637" cy="4" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="52.6441" cy="41" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="2.55431" cy="42" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="29.0191" cy="31" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="127.312" cy="9" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="152.828" cy="42" rx="1.89024" ry="2" fill="#315D3F" />
                                        <path
                                            d="M2.55469 42L29.0181 31L52.6461 41L78.1644 4L102.738 20L127.311 9L152.829 42"
                                            stroke="#315D3F" stroke-width="1.5" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 p-5 rounded">
                        <h2 class="flex justify-between items-center text-slate-400"><strong>PAYMENTS</strong>
                            <StipesIcon />
                        </h2>
                        <div class="flex">
                            <div class="relative">
                                <span class="flex items-end">
                                    <svg width="8" height="11" viewBox="0 0 8 11" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M0.451172 9.30371C0.779297 9.12598 1.11882 8.84115 1.46973 8.44922C1.82064 8.05273 1.99609 7.66536 1.99609 7.28711C1.99609 7.08659 1.95964 6.85872 1.88672 6.60352C1.8457 6.45768 1.77734 6.26172 1.68164 6.01562H0.464844V5.25H1.24414C1.03451 4.83984 0.879557 4.47982 0.779297 4.16992C0.679036 3.85547 0.628906 3.51823 0.628906 3.1582C0.628906 2.45638 0.918294 1.82747 1.49707 1.27148C2.0804 0.715495 2.90983 0.4375 3.98535 0.4375C4.87858 0.4375 5.65332 0.685872 6.30957 1.18262C6.96582 1.6748 7.31445 2.50879 7.35547 3.68457H5.50293C5.45736 3.2334 5.35254 2.86882 5.18848 2.59082C4.94694 2.20345 4.53906 2.00977 3.96484 2.00977C3.54102 2.00977 3.20605 2.14193 2.95996 2.40625C2.71387 2.66602 2.59082 2.9668 2.59082 3.30859C2.59082 3.51367 2.63411 3.73014 2.7207 3.95801C2.80729 4.18132 3.00098 4.61198 3.30176 5.25H5.26367V6.01562H3.5957C3.61393 6.19336 3.6276 6.34147 3.63672 6.45996C3.65039 6.57845 3.65723 6.69694 3.65723 6.81543C3.65723 7.25293 3.52507 7.65853 3.26074 8.03223C3.09212 8.26465 2.75033 8.61784 2.23535 9.0918C2.59993 8.95964 2.86882 8.87305 3.04199 8.83203C3.31999 8.75911 3.59115 8.72266 3.85547 8.72266C4.01953 8.72266 4.17904 8.73633 4.33398 8.76367C4.49349 8.79102 4.66211 8.82975 4.83984 8.87988L5.12012 8.96875C5.24316 9.00977 5.3457 9.03711 5.42773 9.05078C5.51432 9.0599 5.58724 9.06445 5.64648 9.06445C5.85612 9.06445 6.10221 9.00521 6.38477 8.88672C6.54883 8.81836 6.75391 8.71354 7 8.57227L7.56055 10.0967C7.33268 10.2471 7.01367 10.3929 6.60352 10.5342C6.19336 10.6755 5.79915 10.7461 5.4209 10.7461C5.22038 10.7461 5.01074 10.721 4.79199 10.6709C4.57324 10.6208 4.33626 10.5547 4.08105 10.4727L3.69141 10.3428C3.52279 10.2881 3.38379 10.2471 3.27441 10.2197C3.09212 10.1833 2.90527 10.165 2.71387 10.165C2.50879 10.165 2.28776 10.2061 2.05078 10.2881C1.89583 10.3473 1.611 10.4727 1.19629 10.6641L0.451172 9.30371Z"
                                            fill="#292D32" fill-opacity="0.5" />
                                    </svg>
                                    <strong class="text-4xl text-slate-800">524.415£</strong>
                                </span>
                            </div>
                            <div class="flex flex-col ml-4">
                                <span class="flex">13%
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.89645 11.6464C1.70118 11.8417 1.70118 12.1583 1.89645 12.3536C2.09171 12.5488 2.40829 12.5488 2.60355 12.3536L1.89645 11.6464ZM6.58929 7.66071L6.94284 7.30716L6.58929 6.95361L6.23573 7.30716L6.58929 7.66071ZM9.96429 11.0357L9.61073 11.3893L9.96429 11.7428L10.3178 11.3893L9.96429 11.0357ZM15.75 5.25L10.1732 6.74429L14.2557 10.8268L15.75 5.25ZM2.60355 12.3536L6.94284 8.01427L6.23573 7.30716L1.89645 11.6464L2.60355 12.3536ZM6.23573 8.01427L9.61073 11.3893L10.3178 10.6822L6.94284 7.30716L6.23573 8.01427ZM10.3178 11.3893L12.9216 8.78553L12.2145 8.07843L9.61073 10.6822L10.3178 11.3893Z"
                                            fill="#36B07E" />
                                    </svg>
                                </span>
                                <span>vs last 7 days</span>
                            </div>
                            <div class="flex flex-col ml-4">
                                <span>
                                    <svg width="100%" height="44" viewBox="0 0 156 44" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="102.738" cy="20" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="78.1637" cy="4" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="52.6441" cy="41" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="2.55431" cy="42" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="29.0191" cy="31" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="127.312" cy="9" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="152.828" cy="42" rx="1.89024" ry="2" fill="#315D3F" />
                                        <path
                                            d="M2.55469 42L29.0181 31L52.6461 41L78.1644 4L102.738 20L127.311 9L152.829 42"
                                            stroke="#315D3F" stroke-width="1.5" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 p-5 rounded">
                        <h2 class="flex justify-between items-center text-slate-400"><strong>PAYMENTS</strong>
                            <StipesIcon />
                        </h2>
                        <div class="flex">
                            <div class="relative">
                                <span class="flex items-end">
                                    <svg width="8" height="11" viewBox="0 0 8 11" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M0.451172 9.30371C0.779297 9.12598 1.11882 8.84115 1.46973 8.44922C1.82064 8.05273 1.99609 7.66536 1.99609 7.28711C1.99609 7.08659 1.95964 6.85872 1.88672 6.60352C1.8457 6.45768 1.77734 6.26172 1.68164 6.01562H0.464844V5.25H1.24414C1.03451 4.83984 0.879557 4.47982 0.779297 4.16992C0.679036 3.85547 0.628906 3.51823 0.628906 3.1582C0.628906 2.45638 0.918294 1.82747 1.49707 1.27148C2.0804 0.715495 2.90983 0.4375 3.98535 0.4375C4.87858 0.4375 5.65332 0.685872 6.30957 1.18262C6.96582 1.6748 7.31445 2.50879 7.35547 3.68457H5.50293C5.45736 3.2334 5.35254 2.86882 5.18848 2.59082C4.94694 2.20345 4.53906 2.00977 3.96484 2.00977C3.54102 2.00977 3.20605 2.14193 2.95996 2.40625C2.71387 2.66602 2.59082 2.9668 2.59082 3.30859C2.59082 3.51367 2.63411 3.73014 2.7207 3.95801C2.80729 4.18132 3.00098 4.61198 3.30176 5.25H5.26367V6.01562H3.5957C3.61393 6.19336 3.6276 6.34147 3.63672 6.45996C3.65039 6.57845 3.65723 6.69694 3.65723 6.81543C3.65723 7.25293 3.52507 7.65853 3.26074 8.03223C3.09212 8.26465 2.75033 8.61784 2.23535 9.0918C2.59993 8.95964 2.86882 8.87305 3.04199 8.83203C3.31999 8.75911 3.59115 8.72266 3.85547 8.72266C4.01953 8.72266 4.17904 8.73633 4.33398 8.76367C4.49349 8.79102 4.66211 8.82975 4.83984 8.87988L5.12012 8.96875C5.24316 9.00977 5.3457 9.03711 5.42773 9.05078C5.51432 9.0599 5.58724 9.06445 5.64648 9.06445C5.85612 9.06445 6.10221 9.00521 6.38477 8.88672C6.54883 8.81836 6.75391 8.71354 7 8.57227L7.56055 10.0967C7.33268 10.2471 7.01367 10.3929 6.60352 10.5342C6.19336 10.6755 5.79915 10.7461 5.4209 10.7461C5.22038 10.7461 5.01074 10.721 4.79199 10.6709C4.57324 10.6208 4.33626 10.5547 4.08105 10.4727L3.69141 10.3428C3.52279 10.2881 3.38379 10.2471 3.27441 10.2197C3.09212 10.1833 2.90527 10.165 2.71387 10.165C2.50879 10.165 2.28776 10.2061 2.05078 10.2881C1.89583 10.3473 1.611 10.4727 1.19629 10.6641L0.451172 9.30371Z"
                                            fill="#292D32" fill-opacity="0.5" />
                                    </svg>
                                    <strong class="text-4xl text-slate-800">524.415£</strong>
                                </span>
                            </div>
                            <div class="flex flex-col ml-4">
                                <span class="flex">13%
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.89645 11.6464C1.70118 11.8417 1.70118 12.1583 1.89645 12.3536C2.09171 12.5488 2.40829 12.5488 2.60355 12.3536L1.89645 11.6464ZM6.58929 7.66071L6.94284 7.30716L6.58929 6.95361L6.23573 7.30716L6.58929 7.66071ZM9.96429 11.0357L9.61073 11.3893L9.96429 11.7428L10.3178 11.3893L9.96429 11.0357ZM15.75 5.25L10.1732 6.74429L14.2557 10.8268L15.75 5.25ZM2.60355 12.3536L6.94284 8.01427L6.23573 7.30716L1.89645 11.6464L2.60355 12.3536ZM6.23573 8.01427L9.61073 11.3893L10.3178 10.6822L6.94284 7.30716L6.23573 8.01427ZM10.3178 11.3893L12.9216 8.78553L12.2145 8.07843L9.61073 10.6822L10.3178 11.3893Z"
                                            fill="#36B07E" />
                                    </svg>
                                </span>
                                <span>vs last 7 days</span>
                            </div>
                            <div class="flex flex-col ml-4">
                                <span>
                                    <svg width="100%" height="44" viewBox="0 0 156 44" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="102.738" cy="20" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="78.1637" cy="4" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="52.6441" cy="41" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="2.55431" cy="42" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="29.0191" cy="31" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="127.312" cy="9" rx="1.89024" ry="2" fill="#315D3F" />
                                        <ellipse cx="152.828" cy="42" rx="1.89024" ry="2" fill="#315D3F" />
                                        <path
                                            d="M2.55469 42L29.0181 31L52.6461 41L78.1644 4L102.738 20L127.311 9L152.829 42"
                                            stroke="#315D3F" stroke-width="1.5" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Section>
</template>

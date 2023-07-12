<script setup>
import { router, useForm } from "@inertiajs/vue3";
import Section from "@/Components/Section.vue";
import CardIcon from "@/Components/CardIcon.vue";
//import MailchimpIcon from "@/Icons/Mailchimp.vue";
import SendgridIcon from "@/Icons/Sendgrid.vue";
import SendinblueIcon from "@/Icons/Sendinblue.vue";

import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import MailchimpIcon from "@/Icons/MailchimpIcon.vue";
import SendinblueNew from "@/Icons/SendinblueNew.vue";
import SendinGridNew from "@/Icons/SendinGridNew.vue";

const props = defineProps({
    form_data: Object,
});
const form = useForm({
    integration_mailchimp_status: props.form_data.integration_mailchimp_status,
    integration_mailchimp_api_key:
        props.form_data.integration_mailchimp_api_key,
    integration_mailchimp_list_id:
        props.form_data.integration_mailchimp_list_id,
    integration_sendgrid_status: props.form_data.integration_sendgrid_status,
    integration_sendgrid_api_key: props.form_data.integration_sendgrid_api_key,
    integration_sendgrid_list_id: props.form_data.integration_sendgrid_list_id,
    integration_sendinblue_status:
        props.form_data.integration_sendinblue_status,
    integration_sendinblue_api_key:
        props.form_data.integration_sendinblue_api_key,
    integration_sendinblue_list_id:
        props.form_data.integration_sendinblue_list_id,
});
const submitForm = () => {
    form.put(route("partner.settings.integrations"));
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <div class="text-xl font-bold tracking-tight text-gray-900">
            Marketing
        </div>
        <Section bg="bg-white space-y-4">
            <!-- Mailchimp -->
            <CardIcon icon-class="self-start" :height="'h-20'">
                <template #icon>
                    <div class="w-20">
                        <MailchimpIcon />
                    </div>
                </template>

                <template #title> Mailchimp </template>

                <template #default>
                    Run email campaigns for your customers. Everytime new user
                    signs up, we will send a contact information to your
                    Mailchimp account.
                    <div class="mt-4 m-[8px]">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="
                                    form.integration_mailchimp_status
                                "
                                :value="
                                    form.integration_mailchimp_status
                                        ? '1'
                                        : '0'
                                "
                            />
                            <span class="ml-4 text-sm text-gray-700"
                                >Enable</span
                            >
                        </label>
                        <InputError
                            :message="form.errors.integration_mailchimp_status"
                            class="mt-2"
                        />
                    </div>
                    <div
                        v-if="form.integration_mailchimp_status"
                        class="mt-4 space-y-4"
                    >
                        <div>
                            <InputLabel
                                for="integration_mailchimp_api_key"
                                value="API Key"
                            />
                            <div class="text-sm text-gray-600">
                                Create your API key from Extras->API keys
                            </div>
                            <TextInput
                                id="integration_mailchimp_api_key"
                                v-model="form.integration_mailchimp_api_key"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="
                                    form.errors.integration_mailchimp_api_key
                                "
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel
                                for="integration_mailchimp_list_id"
                                value="Audience/List ID"
                            />
                            <div class="text-sm text-gray-600">
                                Copy your Audience ID from Settings->Audience
                                names and defaults
                            </div>
                            <TextInput
                                id="integration_mailchimp_list_id"
                                v-model="form.integration_mailchimp_list_id"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="
                                    form.errors.integration_mailchimp_list_id
                                "
                                class="mt-2"
                            />
                        </div>
                    </div>
                </template>
            </CardIcon>

            <!-- Sendgrid -->
            <CardIcon icon-class="self-start" :height="'h-20'">
                <template #icon>
                    <div class="w-20">
                        <SendgridIcon />
                    </div>
                </template>

                <template #title> Sendgrid </template>

                <template #default>
                    Run email campaigns for your customers. Everytime new user
                    signs up, we will send a contact information to your
                    Sendgrid account.
                    <div class="mt-4 m-[8px]">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="
                                    form.integration_sendgrid_status
                                "
                                :value="
                                    form.integration_sendgrid_status ? '1' : '0'
                                "
                            />
                            <span class="ml-4 text-sm text-gray-700"
                                >Enable</span
                            >
                        </label>
                        <InputError
                            :message="form.errors.integration_sendgrid_status"
                            class="mt-2"
                        />
                    </div>
                    <div
                        v-if="form.integration_sendgrid_status"
                        class="mt-4 space-y-4"
                    >
                        <div>
                            <InputLabel
                                for="integration_sendgrid_api_key"
                                value="API Key"
                            />
                            <div class="text-sm text-gray-600">
                                Create new restricted API key under
                                Settings->API keys.<br />
                                On Access Details list, expand Marketing and set
                                access permission to Full access.<br />
                                Save the API key and store it in safe place.
                            </div>
                            <TextInput
                                id="integration_mailchimp_api_key"
                                v-model="form.integration_sendgrid_api_key"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="
                                    form.errors.integration_sendgrid_api_key
                                "
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel
                                for="integration_sendgrid_list_id"
                                value="List ID"
                            />
                            <div class="text-sm text-gray-600">
                                Create new list from Marketing->Contacts and
                                copy list ID.<br />
                                List ID has a total of 36 characters (a.k.a
                                UUID), e.g
                                12345678-abcd-abcd-abcd-1234567890ab<br />
                                Copy your list ID from browser, e.g.:
                                https://mc.sendgrid.com/contacts/lists/<b
                                    >12345678-abcd-abcd-abcd-1234567890ab</b
                                >
                            </div>
                            <TextInput
                                id="integration_mailchimp_list_id"
                                v-model="form.integration_sendgrid_list_id"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="
                                    form.errors.integration_sendgrid_list_id
                                "
                                class="mt-2"
                            />
                        </div>
                    </div>
                </template>
            </CardIcon>

            <!-- Sendinblue -->
            <CardIcon icon-class="self-start" :height="'h-20'">
                <template #icon>
                    <div class="w-20">
                        <SendinblueIcon />
                    </div>
                </template>

                <template #title> Sendinblue </template>

                <template #default>
                    Run email campaigns for your customers. Everytime new user
                    signs up, we will send a contact information to your
                    Sendinblue account.
                    <div class="m-[8px]">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="
                                    form.integration_sendinblue_status
                                "
                                :value="
                                    form.integration_sendinblue_status
                                        ? '1'
                                        : '0'
                                "
                            />
                            <span class="ml-4 text-sm text-gray-700"
                                >Enable</span
                            >
                        </label>
                        <InputError
                            :message="form.errors.integration_sendinblue_status"
                            class="mt-2"
                        />
                    </div>
                    
                    <div
                        v-if="form.integration_sendinblue_status"
                        class="mt-4 space-y-4"
                    >
                        <div>
                            <InputLabel
                                for="integration_sendinblue_api_key"
                                value="API Key"
                            />
                            <TextInput
                                id="integration_mailchimp_api_key"
                                v-model="form.integration_sendinblue_api_key"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="
                                    form.errors.integration_sendinblue_api_key
                                "
                                class="mt-2"
                            />
                        </div>
                        <div>
                            <InputLabel
                                for="integration_sendinblue_list_id"
                                value="List ID"
                            />
                            <TextInput
                                id="integration_mailchimp_list_id"
                                v-model="form.integration_sendinblue_list_id"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="
                                    form.errors.integration_sendinblue_list_id
                                "
                                class="mt-2"
                            />
                        </div>
                    </div>
                </template>
            </CardIcon>
        </Section>

        <div
            class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md"
        >
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing" type="submit"
            >
                Save
            </ButtonLink>
        </div>
    </form>

            <div class="bg-white bg-white-50 p-3 pt-3 gap-4 relative transition rounded mt-4">
                <div class="text-2xl font-bold tracking-tight text-gray-900 pt-2 pb-4">
                    Marketing
                </div>
                <div class="bg-mainBg p-5 rounded mt-3 mb-3">
                    <div class="flex flex-wrap justify-between items-center border-b-2 border-slate-300  pb-4">
                        <div>
                            <MailchimpIcon />
                        </div>
                            <div>
                                <label class="flex items-center">
                                <Checkbox
                                    v-model:checked="
                                        form.integration_sendinblue_status
                                    "
                                    :value="
                                        form.integration_sendinblue_status
                                            ? '1'
                                            : '0'
                                    "
                                />
                                <span class="ml-2 text-sm text-gray-700"
                                    ><strong>Enable</strong></span
                                >
                            </label>
                            <InputError
                                :message="form.errors.integration_sendinblue_status"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div class="w-full pt-5">
                        <h3 class="text-2xl pb-4"><strong>Mailchimp</strong></h3>
                        <p><span class="mr-2 border-l-4 border-[#315D3F] rounded-md border-t-[4px]"></span>Run email campaigns for your customers. Everytime new user signs up, we will send a contact information to your Mailchimp account.</p>
                    </div>
                    <div class="grid gap-4 grid-cols-2 mt-5">
                        <div class="grow-none">
                            <InputLabel
                                for="integration_sendinblue_api_key"
                                value="API Key"
                                
                            />
                            <TextInput
                                id="integration_mailchimp_api_key"
                                v-model="form.integration_sendinblue_api_key"
                                type="text"
                                class="mt-1 block w-full h-14"
                            />
                            <div class="text-sm text-gray-600 pt-2">
                                Create your API key from Extras->API keys
                            </div>
                        </div>
                        <div class="grow">
                            <InputLabel
                                for="integration_sendinblue_list_id"
                                value="List ID"
                            />
                            <TextInput
                                id="integration_mailchimp_list_id"
                                v-model="form.integration_sendinblue_list_id"
                                type="text"
                                class="mt-1 block w-full h-14"
                            />
                            <div class="text-sm text-gray-600 pt-2">
                                YEAR-Copy your Audience ID from Settings->Audience names and defaults-DAY with leading zeros
                            </div>
                        </div>
                    </div>
                </div>
                <!--sendgrid-->
                <div class="bg-mainBg p-5 rounded mt-3 mb-3">
                    <div class="flex flex-wrap justify-between items-center border-b-2 border-slate-300  pb-4">
                        <div>
                            <SendinGridNew  />
                        </div>
                            <div>
                                <label class="flex items-center">
                                <Checkbox
                                    v-model:checked="
                                        form.integration_sendinblue_status
                                    "
                                    :value="
                                        form.integration_sendinblue_status
                                            ? '1'
                                            : '0'
                                    "
                                />
                                <span class="ml-2 text-sm text-gray-700"
                                    ><strong>Enable</strong></span
                                >
                            </label>
                            <InputError
                                :message="form.errors.integration_sendinblue_status"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div class="w-full pt-5">
                        <h3 class="text-2xl pb-4"><strong>Sendgrid</strong></h3>
                        <p><span class="mr-2 border-l-4 border-[#315D3F] rounded-md border-t-[4px]"></span>Run email campaigns for your customers. Everytime new user signs up, we will send a contact information to your Sendgrid account.</p>
                    </div>
                    <div class="grid gap-4 grid-cols-2 mt-5">
                        <div class="grow-none">
                            <InputLabel
                                for="integration_sendinblue_api_key"
                                value="API Key"
                                
                            />
                            <TextInput
                                id="integration_mailchimp_api_key"
                                v-model="form.integration_sendinblue_api_key"
                                type="text"
                                class="mt-1 block w-full h-14"
                            />
                            <div class="text-sm text-gray-600 pt-2">
                                <ul>
                                    <li>Create new restricted API key under Settings->API keys.</li>
                                    <li>On Access Details list, expand Marketing and set access permission to Full access.</li>
                                    <li>Save the API key and store it in safe place.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="grow">
                            <InputLabel
                                for="integration_sendinblue_list_id"
                                value="List ID"
                            />
                            <TextInput
                                id="integration_mailchimp_list_id"
                                v-model="form.integration_sendinblue_list_id"
                                type="text"
                                class="mt-1 block w-full h-14"
                            />
                            <div class="text-sm text-gray-600 pt-2">
                                <ul class="flex flex-wrap">
                                    <li>Create new list from Marketing->Contacts and copy list ID.</li>
                                    <li>List ID has a total of 36 characters (a.k.a UUID),
                                        <span class="text-blue"><strong> e.g 12345678-abcd-abcd-abcd-1234567890ab.</strong></span></li>
                                    <li class="break-all">Copy your list ID from browser, e.g.:
                                        <span class="text-blue"><strong>https://mc.sendgrid.com/contacts/lists/12345678-abcd-abcd-abcd-1234567890ab</strong>
                                        </span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--sendinblue-->
                <div class="bg-mainBg p-5 rounded mt-3 mb-3">
                    <div class="flex flex-wrap justify-between items-center border-b-2 border-slate-300  pb-4">
                        <div>
                            <SendinblueNew />
                        </div>
                            <div>
                                <label class="flex items-center">
                                <Checkbox
                                    v-model:checked="
                                        form.integration_sendinblue_status
                                    "
                                    :value="
                                        form.integration_sendinblue_status
                                            ? '1'
                                            : '0'
                                    "
                                />
                                <span class="ml-2 text-sm text-gray-700"
                                    ><strong>Enable</strong></span
                                >
                            </label>
                            <InputError
                                :message="form.errors.integration_sendinblue_status"
                                class="mt-2"
                            />
                        </div>
                    </div>
                    <div class="w-full pt-5">
                        <h3 class="text-2xl pb-4"><strong>Sendinblue</strong></h3>
                        <p><span class="mr-2 border-l-4 border-[#315D3F] rounded-md border-t-[4px]"></span>Run email campaigns for your customers. Everytime new user signs up, we will send a contact information to your Sendinblue account.</p>
                    </div>
                    <div class="grid gap-4 grid-cols-2 mt-5">
                        <div class="grow-none">
                            <InputLabel
                                for="integration_sendinblue_api_key"
                                value="API Key"
                                
                            />
                            <TextInput
                                id="integration_mailchimp_api_key"
                                v-model="form.integration_sendinblue_api_key"
                                type="text"
                                class="mt-1 block w-full h-14"
                            />
                            <div class="text-sm text-gray-600 pt-2">
                                Create your API key from Extras->API keys
                            </div>
                        </div>
                        <div class="grow">
                            <InputLabel
                                for="integration_sendinblue_list_id"
                                value="List ID"
                            />
                            <TextInput
                                id="integration_mailchimp_list_id"
                                v-model="form.integration_sendinblue_list_id"
                                type="text"
                                class="mt-1 block w-full h-14"
                            />
                            <div class="text-sm text-gray-600 pt-2">
                                YEAR-Copy your Audience ID from Settings->Audience names and defaults-DAY with leading zeros
                            </div>
                        </div>
                    </div>
                </div>
                <!--sendinblue-->
            </div>





</template>

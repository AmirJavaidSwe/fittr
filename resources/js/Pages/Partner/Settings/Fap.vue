<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    form_data: Object,
    business_seetings: Object,
    fap_packs: Array,
});

const form = useForm({
    fap_status: props.form_data.fap_status,
});

const price_fields = {
    id: null,
    fap_value: null,
};
const form_price = useForm(price_fields);

const showEdit = (price) => {
    if(form_price.edit){
        //save action
        return updatePrice();
    }
    //set price values edited to form_price by price_fields
    for (const property in price_fields) {
        form_price[property] = price[property];
        //Any props added to initialized form will not be included to post requests. Form is still a reactive object.
        form_price['edit'] = true;
    }
};
const cancelEdit = () => {
    form_price.edit = false;
};
const updatePrice = () => {
    form_price
    .transform((data) => ({
        ...data,
        'action': 'edit'
    }))
    .put(route('partner.packs.price.update', { price: form_price.id }), {
        preserveScroll: true,
        onSuccess: () => {
            cancelEdit();
        },
    });
};
const editId = ref(null);
const isEdit = (id) => {
    return form_price.edit && form_price.id === id;
};

// confirmation modal:
const modalShown = ref(false);
const showConfirmation = () => {
    modalShown.value = true;
};
const modalConfirmed = () => {
    form.put(route('partner.settings.fap'), {
        preserveScroll: true,
        onSuccess: () => {
            modalShown.value = false;
        },
    });
};
</script>

<template>
    <FormSection @submitted="showConfirmation">
        <template #description>
            Fair access policy helps preventing usage abuse on unlimited memberships.
            When enabled, members having unlimited memberships will not be able to book more classes for given day than defined by policy.
        </template>

        <template #form>
            <!-- Global FAP Status -->
            <div class="col-span-6 sm:col-span-4">
                <Switcher
                    v-model="form.fap_status"
                    title="Status"
                    description="Enable or disable the policy."
                />
                <InputError :message="form.errors.fap_status" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>

    <ConfirmationModal :show="modalShown" @close="modalShown = false">
        <template #title>
            Confirmation required
        </template>

        <template #content>
            Are you sure you would like to {{form.fap_status ? 'enable' : 'disable'}} the policy?
        </template>

        <template #footer>
            <ButtonLink
                styling="default"
                size="default"
                @click="modalShown = false">
                Cancel
            </ButtonLink>

            <ButtonLink
                styling="primary"
                size="default"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="modalConfirmed"
            >
                Confirm
            </ButtonLink>
        </template>
    </ConfirmationModal>

    <div class="my-4 px-4 py-5 bg-white sm:p-6 shadow sm:rounded">
        <div class="text-xl font-medium">Pricing options with enabled FAP</div>
        <div v-for="fp in fap_packs" :key="fp.id" class="p-2 my-2 flex flex-wrap">
            <div class="w-full md:w-60 bg-gray-50 p-2">
                <div class="font-bold text-xl">{{fp.title}}</div>
                <div v-if="fp.sub_title" class="text-sm">{{fp.sub_title}}</div>
                <ButtonLink
                    styling="primary"
                    size="small"
                    class="mt-2"
                    :href="route('partner.packs.edit', fp)"
                >
                    Edit
                </ButtonLink>
            </div>
            <div class="flex-1">
                <div v-for="price in fp.prices" :key="price.id" class="border flex flex-wrap gap-x-8 items-center p-2">
                    <div><span class="font-bold text-3xl" :title="price.currency.toUpperCase()">{{price.price_formatted}}</span></div>
                    <div>{{ price.interval_human }}</div>
                    <div>
                        <div v-if="isEdit(price.id)">
                            <TextInput
                                v-model="form_price.fap_value"
                                class="w-40"
                                type="number"
                                min="1"
                                max="100"
                                autofocus
                            />
                            <InputError :message="form_price.errors.fap_value" class="mt-2" />
                        </div>
                        <div class="w-40 h-10 flex items-center" v-else>
                            Fapped at {{price.fap_value}} classes
                        </div>
                    </div>
                    <ButtonLink
                        size="small"
                        styling="blank"
                        v-if="isEdit(price.id)"
                        @click="cancelEdit()"
                    >
                        Cancel
                    </ButtonLink>
                    <ButtonLink
                        :styling="isEdit(price.id) ? 'primary' : 'secondary'"
                        size="small"
                        @click="showEdit(price)"
                        :class="{ 'opacity-25': form_price.processing }"
                        :disabled="form_price.processing || (form_price.edit && form_price.id !== price.id)"
                    >
                        {{ isEdit(price.id) ? 'Save' : 'Change'}}
                    </ButtonLink>
                    
                </div>
            </div>
        </div>
    </div>
</template>

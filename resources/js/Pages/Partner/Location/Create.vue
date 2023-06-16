<script setup>

import FormSection from "@/Components/FormSection.vue";
import Form from "./Form.vue";
import {useForm} from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    users: Array,
    amenities: Array,
    countries: Array
});

const storeLocation = () => {
    form.post(route('partner.locations.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset().clearErrors()
    });
};

let form = useForm({
    title: '',
    brief: '',
    manager_id: '',
    manager_email: '',
    address_line_1: '',
    address_line_2: '',
    country_id: '',
    city: '',
    postcode: '',
    map_latitude: '',
    map_longitude: '',
    tel: '',
    email: '',
    amenity_ids: [],
    image: null,
    status: true,
});

</script>

<template>
    <FormSection title="Create Location">
        <template #form>
            <Form :form="form" :users="users" :amenities="amenities" :countries="countries" />
        </template>
        <template #actions>
            <ButtonLink
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="submit"
                @click="storeLocation"
            >
                <span>Create</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>
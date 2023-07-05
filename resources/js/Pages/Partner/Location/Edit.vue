<script setup>

import Form from "./Form.vue";
import {router, useForm} from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import FormSection from "@/Components/FormSection.vue";
import { watch } from "vue";

const updateLocation = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('partner.locations.update', props.location), {
        preserveScroll: true,
        // onSuccess: () => form.reset()
    });
};

let props = defineProps({
    location: {
        type: Object,
        required: true
    },
    users: Array,
    amenities: Array,
    countries: Array,
    studios: Array,
});

let form = useForm({
    title: props.location?.title,
    brief: props.location?.brief,
    manager_id: props.location?.manager?.id,
    manager_email: props.location?.manager?.email,
    address_line_1: props.location?.address_line_1,
    address_line_2: props.location?.address_line_2,
    country_id: props.location?.country_id,
    city: props.location?.city,
    postcode: props.location?.postcode,
    map_latitude: props.location?.map_latitude,
    map_longitude: props.location?.map_longitude,
    tel: props.location?.tel,
    email: props.location?.email,
    amenity_ids: props.location?.amenities.map(item => item.id),
    image: null,
    uploaded_images: [...props.location?.images],
    status: !!props.location?.status,
    studio_ids: props.location?.studios.map(item => item.id),
});

const fileRemoveForm = useForm({
    image_id: '',
});

const removeUploadedFile = (id) => {
    fileRemoveForm.image_id = id;
    fileRemoveForm.delete(route('partner.locations.delete-image', props.location?.id), {
        onSuccess: () => {
            form.uploaded_images = [...props.location?.images];
        }
    });
}

</script>

<template>
    <FormSection>
        <template #form>
            <Form
                :form="form"
                :users="users"
                :amenities="amenities"
                :countries="countries"
                :studios="studios"
                :editMode="true"
                @remove_uploaded_file="removeUploadedFile"
            />
        </template>
        <template #actions>
            <ButtonLink
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="submit"
                @click="updateLocation"
            >
                <span>Save changes</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>
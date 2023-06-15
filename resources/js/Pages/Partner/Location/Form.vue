<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import { ref } from "vue";
import Dropzone from "@/Components/Dropzone.vue";
import MultiselectInput from "@/Components/MultiselectInput.vue";
import { computed } from "@vue/reactivity";

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    users: Array,
    amenities: Array,
    countries: Array
})

const steps = ref([
    {
        name: 'Location',
        active: true,
        completed: false,
    },
    {
        name: 'Administrative',
        active: false,
        completed: false,
    },
]);

const manager = computed(() => {
    return props.users.find(item => item.id == props.form.manager_id);
});

</script>

<template>
    <!-- <div class="mb-5 font-bold">
        Details:
    </div> -->
    <div class="mb-3">
        <InputLabel for="name" value="Name" />
        <TextInput
            id="name"
            v-model="form.title"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.title" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="description" value="Description" />
        <TextInput
            id="description"
            v-model="form.brief"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.brief" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="manager" value="General Manager" />
        <SelectInput
            id="manager"
            v-model="form.manager_id"
            :options="users"
            option_value="id"
            option_text="name"
            class="mt-1 block w-full"
        >
        </SelectInput>
        <InputError :message="form.errors.manager_id" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="manager_email" value="Email (General Manager)" />
        <TextInput
            id="manager_email"
            :value="manager?.email"
            type="text"
            class="mt-1 block w-full"
            disabled="true"
            />
        <!-- <InputError :message="form.errors.manager_email" class="mt-2" /> -->
    </div>
    <!-- <div class="my-4 border-t border-gray-300"></div>
    <div class="mb-5 font-bold">
        Address/Contact:
    </div> -->
    <div class="mb-3">
        <InputLabel for="address_line_1" value="Address Line 1" />
        <TextInput
            id="address_line_1"
            v-model="form.address_line_1"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.address_line_1" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="address_line_2" value="Address Line 2" />
        <TextInput
            id="address_line_2"
            v-model="form.address_line_2"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.address_line_2" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="country_id" value="Country" />
        <SelectInput
            id="country_id"
            v-model="form.country_id"
            :options="countries"
            option_value="id"
            option_text="name"
            class="mt-1 block w-full"
        >
        </SelectInput>
        <InputError :message="form.errors.country_id" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="city" value="City/Town" />
        <TextInput
            id="city"
            v-model="form.city"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.city" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="postcode" value="Postcode" />
        <TextInput
            id="postcode"
            v-model="form.postcode"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.postcode" class="mt-2" />
    </div>
    <div class="mb-3">
        <div class="inline-block w-1/2 pr-2">
            <InputLabel for="map_latitude" value="Latitude" />
            <TextInput
                id="map_latitude"
                v-model="form.map_latitude"
                type="text"
                class="mt-1 block w-full"
                />
            <InputError :message="form.errors.map_latitude" class="mt-2" />
        </div>
        <div class="inline-block w-1/2 pl-2">
            <InputLabel for="map_longitude" value="Longitude" />
            <TextInput
                id="map_longitude"
                v-model="form.map_longitude"
                type="text"
                class="mt-1 block w-full"
                />
            <InputError :message="form.errors.map_longitude" class="mt-2" />
        </div>
    </div>
    <div class="mb-3">
        <InputLabel for="phone" value="Phone" />
        <TextInput
            id="phone"
            v-model="form.tel"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.tel" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="email" value="Email" />
        <TextInput
            id="email"
            v-model="form.email"
            type="text"
            class="mt-1 block w-full"
            />
        <InputError :message="form.errors.email" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="image" value="Image" />
        <Dropzone id="image" v-model="form.image" :uploaded_files="form.uploaded_images" :accept="['.jpg', '.png', '.bmp']" max_width="200" max_height="200" />
        <InputError :message="form.errors.image" class="mt-2" />
    </div>
    <div class="mb-3">
        <InputLabel for="amenities" value="Amenities" class="mt-4" />
        <MultiselectInput :options="amenities" v-model="form.amenity_ids" mode="multiple" />
        <InputError :message="form.errors.amenity_ids" class="mt-2" />
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>
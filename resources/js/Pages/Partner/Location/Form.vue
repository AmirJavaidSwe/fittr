<script setup>
import { watchEffect, computed, ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Dropzone from "@/Components/Dropzone.vue";
import MultiselectInput from "@/Components/MultiselectInput.vue";
import Switcher from "@/Components/Switcher.vue";
import Multiselect from "@vueform/multiselect";
import Avatar from "@/Components/Avatar.vue";
import "@vueform/multiselect/themes/tailwind.css";
// import UploadFileIcon from "@/Icons/Upload.vue";
// import UploadingIcon from "@/Icons/Uploading.vue";

const emit = defineEmits(["remove_uploaded_file", "create_new_gm", 'create_new_amenity']);

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  users: Array,
  amenities: Array,
  countries: Array,
  studios: Array,
  editMode: Boolean,
  modal: Boolean
});
const manager = computed(() => {
  return props.users.find((item) => item.id == props.form.manager_id);
});

const amenitiesList = computed(() => {
  let amenities = [];
    for (let i = 0; i < props.amenities.length; i++) {
        amenities.push({
            value: props.amenities[i].value,
            label: props.amenities[i].label,
        });
    }
    amenities.push({
      value: "create_new_amenity",
      label: "Add New"
    });
    return amenities;
});

const gmChanged = () => {
  if (props.form.manager_id == "create_new_user") {
    emit("create_new_gm");
  }
};


watchEffect(() => {
  if (props.form.amenity_ids.includes("create_new_amenity")) {
    emit("create_new_amenity");
  }
})


const usersList = computed(() => {
    let users = [];
    for (let i = 0; i < props.users.length; i++) {
        users.push({
            value: props.users[i].id,
            label: props.users[i].name,
        });
    }
    return users;
});
</script>

<template>
  <div class="my-3">
    <InputLabel for="title" value="Title" />
    <TextInput
      id="name"
      v-model="form.title"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.title" class="mt-2" />
  </div>
  <div class="my-3">
    <InputLabel for="description" value="Description" />
    <TextInput
      id="description"
      v-model="form.brief"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.brief" class="mt-2" />
  </div>
  <div class="my-3">
    <div class="">
      <InputLabel for="manager" value="General Manager" />
      <Multiselect
        id="manager"
        v-model="form.manager_id"
        :options="usersList"
        :searchable="true"
        :close-on-select="true"
        :show-labels="true"
        placeholder="Select General Manager"
        @select="gmChanged"
      >
        <template v-slot:singlelabel="{ value }">
          <div class="multiselect-single-label flex items-center">
            <Avatar
              size="small"
              :title="value.label"
              v-if="value.label != 'Add New'"
            />
            <span class="ml-2">{{ value.label }}</span>
          </div>
        </template>

        <template v-slot:option="{ option }">
          <Avatar
            size="small"
            :title="option.label"
            v-if="option.label != 'Add New'"
          />
          <span class="ml-5">{{ option.label }}</span>
        </template>
      </Multiselect>
      <InputError :message="form.errors.manager_id" class="mt-2" />
    </div>
  </div>
  <div class="my-3">
    <InputLabel for="manager_email" value="Email (General Manager)" />
    <TextInput
      id="manager_email"
      :value="manager?.email"
      type="text"
      class="mt-1 block w-full"
      disabled="true"
    />
  </div>
  <div class="my-3">
    <InputLabel for="address_line_1" value="Address Line 1" />
    <TextInput
      id="address_line_1"
      v-model="form.address_line_1"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.address_line_1" class="mt-2" />
  </div>
  <div class="my-3">
    <InputLabel for="address_line_2" value="Address Line 2" />
    <TextInput
      id="address_line_2"
      v-model="form.address_line_2"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.address_line_2" class="mt-2" />
  </div>
  <div class="my-3">
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
  <div class="my-3">
    <InputLabel for="city" value="City/Town" />
    <TextInput
      id="city"
      v-model="form.city"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.city" class="mt-2" />
  </div>
  <div class="my-3">
    <InputLabel for="postcode" value="Postcode" />
    <TextInput
      id="postcode"
      v-model="form.postcode"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.postcode" class="mt-2" />
  </div>
  <div class="my-3">
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
  <div class="my-3">
    <InputLabel for="phone" value="Phone" />
    <TextInput
      id="phone"
      v-model="form.tel"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.tel" class="mt-2" />
  </div>
  <div class="my-3">
    <InputLabel for="email" value="Email" />
    <TextInput
      id="email"
      v-model="form.email"
      type="text"
      class="mt-1 block w-full"
    />
    <InputError :message="form.errors.email" class="mt-2" />
  </div>
  <div class="my-3">
    <InputLabel for="image" value="Image" />
    <Dropzone
      id="image"
      v-model="form.image"
      :modal="props.modal"
      :uploaded_files="form.uploaded_images ? form.uploaded_images : []"
      :accept="['.jpg', '.png', '.bmp', '.svg']"
      max_width="200"
      max_height="200"
      :buttonText="'Select new image'"
      @remove_uploaded_file="$emit('remove_uploaded_file', $event)"
    />
    <InputError :message="form.errors.image" class="mt-2" />
  </div>
  <div class="my-3">
    <InputLabel for="amenities" value="Amenities" />
    <MultiselectInput
      :options="amenitiesList"
      v-model="form.amenity_ids"
      mode="multiple"
    />
    <InputError :message="form.errors.amenity_ids" class="mt-2" />
  </div>
  <div class="my-3" v-if="editMode">
    <InputLabel for="studios" value="Studios" />
    <MultiselectInput
      :options="studios"
      v-model="form.studio_ids"
      mode="multiple"
    />
    <InputError :message="form.errors.studio_ids" class="mt-2" />
  </div>
  <div class="my-3">
    <Switcher v-model="form.status" title="Status" />
    <InputError :message="form.errors.status" class="mt-2" />
  </div>
</template>
<style src="@vueform/multiselect/themes/tailwind.css"></style>
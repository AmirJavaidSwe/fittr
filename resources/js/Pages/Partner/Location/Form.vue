<script setup>
import { watchEffect, watch, computed, ref, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Dropzone from "@/Components/Dropzone.vue";
import MultiselectInput from "@/Components/MultiselectInput.vue";
import Switcher from "@/Components/Switcher.vue";
import Multiselect from "@vueform/multiselect";
import Avatar from "@/Components/Avatar.vue";
import intlTelInput from "intl-tel-input";
import "intl-tel-input/build/css/intlTelInput.css";
import { parsePhoneNumber, AsYouType } from "libphonenumber-js";
import { Loader } from '@googlemaps/js-api-loader';

const emit = defineEmits([
    "remove_uploaded_file",
    "create_new_gm",
    "create_new_amenity",
    'createNewStudio'
]);

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    users: Array,
    amenities: Array,
    countries: Array,
    studios: Array,
    modal: Boolean,
});

const { form } = props;
const phoneNumber = ref(null);
phoneNumber.value = form.tel;


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
        label: "Add New",
    });
    return amenities;
});

const gmChanged = () => {
    if (props.form.manager_id == "create_new_user") {
        emit("create_new_gm");
    }
};

watchEffect(() => {
    if (!phoneNumber.value || !phoneNumber.value.startsWith("+")) {
        phoneNumber.value = "+" + phoneNumber.value;
    }
});

watchEffect(() => {
    if (props.form.amenity_ids.includes("create_new_amenity")) {
        emit("create_new_amenity");
    }
});

watchEffect(() => {
    if (props.form.studio_ids && props.form.studio_ids.length && props.form.studio_ids.includes("create_new_studio")) {
        emit("createNewStudio");
    }
});

const usersList = computed(() => {
    const found = props.users.find((element) => element.id == 'create_new_user');
    if(!!found === false){
        props.users.push({id: 'create_new_user', full_name: 'Add New'});
    }

    return props.users;
});

const studioList = computed(() => {
    let studios = [];
    for (let i = 0; i < props.studios.length; i++) {
        studios.push({
            value: props.studios[i].value,
            label: props.studios[i].label,
        });
    }
    studios.push({
        value: "create_new_studio",
        label: "Add New",
    });

    return studios;
});

watch(phoneNumber, (newVal, oldVal) => {
    formatPhoneInput();
});

onMounted(async () => {
    const input = document.querySelector("#phone");
    intlTelInput(input, {
        utilsScript: await import("intl-tel-input/build/js/utils.js"),
        autoInsertDialCode: true,
        formatOnDisplay: true,
        nationalMode: false,
        customContainer: "w-full",
    });
    formatPhoneInput();
});
const formatPhoneInput = () => {
    setTimeout(() => {
        phoneNumber.value = phoneNumber.value.replace(/\D+/, "");
        if (!phoneNumber.value || !phoneNumber.value.startsWith("+")) {
            phoneNumber.value = "+" + phoneNumber.value;
        }
        let letFormattedNumber = "";
        try {
            const newNumber = parsePhoneNumber(phoneNumber.value);
            letFormattedNumber = new AsYouType().input(phoneNumber.value);
            phoneNumber.value = letFormattedNumber
                ? letFormattedNumber
                : phoneNumber.value;
            form.tel = phoneNumber.value;
        } catch (error) {
            phoneNumber.value = phoneNumber.value.replace(/\D+/, "");
            form.tel = phoneNumber.value;
        }
    }, 100);
};

//GOOGLE MAPS + Autocomplete
const apiKey = usePage().props.google_maps_key;
const loader = new Loader({
    apiKey: apiKey,
    version: "weekly",
    language: "en",
    region: "GB",
    libraries: ["places", "maps", "marker"]
});

let googlemaps; //google.maps library
let autocomplete;//Autocomplete
let map; //Map
let marker; //AdvancedMarkerElement
const showMap = computed(() => {
  return props.form.map_latitude && props.form.map_longitude;
});
loader
    .load()
    .then(async () => {
        googlemaps = await google.maps;
        initAutocomplete();
        initMap();
    })
    .catch((e) => {
        console.log(e)
    });

const initAutocomplete = () => {
    const input = document.querySelector("#address_line_1");
    autocomplete = new googlemaps.places.Autocomplete(
        input,
        {
            componentRestrictions: { country: "uk" }, //either remove restrictions or use business settings
            language: "en",
            types: ["geocode"], //https://developers.google.com/maps/documentation/javascript/supported_types
            fields: ["geometry", "address_components"],
        }
    );

    autocomplete.addListener("place_changed", () => {
        //defaults when no results from getPlace()
        let place = {
            address_components: [],
            geometry: null,
            ...autocomplete.getPlace()
        };

        place.address_components.forEach((component) => {
            component.types.forEach((type) => {
                if(type == 'street_number'){
                    props.form.address_line_1 = component.long_name;
                }
                if(type == 'route'){
                    props.form.address_line_1 = props.form.address_line_1 + ' ' + component.long_name;
                }
                if(type == 'postal_town'){
                    props.form.city = component.long_name;
                }
                if(type == 'postal_code'){
                    props.form.postcode = component.long_name;
                }
                if(type == 'country'){
                    props.form.country_id = props.countries.find((element) => element.name = component.long_name).id;
                }
            });
        });
        //set input visible text to v-model:
        input.value = props.form.address_line_1;

        //set coordinates
        if(place.geometry?.location){
            props.form.map_latitude = place.geometry.location.lat();
            props.form.map_longitude = place.geometry.location.lng();
        }
    });
};

const initMap = () => {
    const map_area = document.getElementById('map_area');
    const myLatLng = { lat: parseFloat(props.form.map_latitude ?? 51.5281798), lng: parseFloat(props.form.map_longitude ?? -0.4312316) };
    map = new googlemaps.Map(map_area, {
        center: myLatLng,
        zoom: 13,
    });
    marker = new googlemaps.Marker({
        position: myLatLng,
        map: map,
  });
};
//watch changes to lat and lng made from new place found or manual coordinates change: move map and marker
watch(
  [
    () => form.map_latitude,
    () => form.map_longitude
  ],
  ([newLat, NewLng]) => {
    let lat = newLat.length === 0 ? 51.5281798 : parseFloat(newLat);
    let lng = NewLng.length === 0 ? -0.4312316 : parseFloat(NewLng);

    map.panTo({ lat, lng });
    marker.setPosition({ lat, lng });
  }
);
</script>

<template>
<div class="space-y-4">
    <div>
        <InputLabel for="title" value="Title" />
        <TextInput
            id="name"
            v-model="form.title"
            type="text"
            class="block w-full"
        />
        <InputError :message="form.errors.title" class="mt-2" />
    </div>
    <div>
        <InputLabel for="description" value="Description" />
        <TextInput
            id="description"
            v-model="form.brief"
            type="text"
            class="block w-full"
        />
        <InputError :message="form.errors.brief" class="mt-2" />
    </div>
    <div>
        <div>
            <InputLabel for="manager" value="General Manager" />
            <Multiselect
                id="manager"
                v-model="form.manager_id"
                :options="usersList"
                valueProp="id"
                trackBy="full_name"
                :searchable="true"
                :closeOnSelect="true"
                placeholder="Select General Manager"
                @select="gmChanged"
            >
                <template v-slot:singlelabel="{ value }">
                    <div class="multiselect-single-label flex items-center">
                        <Avatar v-if="value.id != 'create_new_user'" size="small" :initials="value.initials" :imageUrl="value.profile_photo_url" />
                        <span class="ml-2">{{ value.full_name }}</span>
                    </div>
                </template>

                <template v-slot:option="{ option }">
                    <Avatar size="small" :initials="option.initials" :imageUrl="option.profile_photo_url" v-if="option.id != 'create_new_user'" />
                    <span class="ml-4">{{ option.full_name }}</span>
                </template>
            </Multiselect>
            <InputError :message="form.errors.manager_id" class="mt-2" />
        </div>
    </div>

    

    <!-- <div class="relative">
        <input type="text" ref="ainput" placeholder="search" class="w-full" />

    </div> -->

    


    <div>
        <InputLabel for="address_line_1" value="Address Line 1" />
        <TextInput
            id="address_line_1"
            v-model="form.address_line_1"
            autocomplete="one-time-code"
            type="text"
            placeholder="Search"
            class="block w-full"
        />
        <InputError :message="form.errors.address_line_1" class="mt-2" />
    </div>
    <div>
        <InputLabel for="address_line_2" value="Address Line 2" />
        <TextInput
            id="address_line_2"
            v-model="form.address_line_2"
            type="text"
            class="block w-full"
        />
        <InputError :message="form.errors.address_line_2" class="mt-2" />
    </div>
    <div class="flex flex-wrap gap-2">
        <div>
            <InputLabel for="country_id" value="Country" />
            <SelectInput
                id="country_id"
                v-model="form.country_id"
                :options="countries"
                option_value="id"
                option_text="name"
                class="block w-full"
            >
            </SelectInput>
            <InputError :message="form.errors.country_id" class="mt-2" />
        </div>
        <div class="flex-1">
            <InputLabel for="city" value="City/Town" />
            <TextInput
                id="city"
                v-model="form.city"
                type="text"
                class="block w-full"
            />
            <InputError :message="form.errors.city" class="mt-2" />
        </div>
        <div class="w-28">
            <InputLabel for="postcode" value="Postcode" />
            <TextInput
                id="postcode"
                v-model="form.postcode"
                type="text"
                class="block w-full"
            />
            <InputError :message="form.errors.postcode" class="mt-2" />
        </div>
    </div>


    <div>
        <div class="inline-block w-1/2 pr-2">
            <InputLabel for="map_latitude" value="Latitude" />
            <TextInput
                id="map_latitude"
                v-model="form.map_latitude"
                type="text"
                class="block w-full"
            />
            <InputError :message="form.errors.map_latitude" class="mt-2" />
        </div>
        <div class="inline-block w-1/2 pl-2">
            <InputLabel for="map_longitude" value="Longitude" />
            <TextInput
                id="map_longitude"
                v-model="form.map_longitude"
                type="text"
                class="block w-full"
            />
            <InputError :message="form.errors.map_longitude" class="mt-2" />
        </div>
    </div>
    <div id="map_area" v-show="showMap" class="h-80"></div>
    
    <div class="flex flex-wrap gap-2">
        <div>
            <InputLabel for="phone" value="Phone" />

            <TextInput
                id="phone"
                v-model="phoneNumber"
                type="text"
                maxlength="16"
                class="block w-full"
            />
            <InputError :message="form.errors.tel" class="mt-2" />
        </div>
        <div class="flex-grow">
            <InputLabel for="email" value="Contact Email" />
            <TextInput
                id="email"
                v-model="form.email"
                autocomplete="one-time-code"
                type="text"
                class="block w-full"
            />
            <InputError :message="form.errors.email" class="mt-2" />
        </div>
    </div>
    <div>
        <InputLabel for="image" value="Image" />
        <Dropzone
            id="image"
            v-model="form.image"
            :modal="props.modal"
            :uploaded_files="form.uploaded_images ? form.uploaded_images : []"
            :accept="['.jpg', '.png', '.webp', '.svg']"
            max_width="200"
            max_height="200"
            :buttonText="'Select new image'"
            @remove_uploaded_file="$emit('remove_uploaded_file', $event)"
        />
        <InputError :message="form.errors.image" class="mt-2" />
    </div>
    <div>
        <InputLabel for="amenities" value="Amenities" />
        <MultiselectInput
            :options="amenitiesList"
            v-model="form.amenity_ids"
            mode="multiple"
        />
        <InputError :message="form.errors.amenity_ids" class="mt-2" />
    </div>
    <div>
        <InputLabel for="studios" value="Studios" />
        <MultiselectInput
            :options="studioList"
            v-model="form.studio_ids"
            mode="multiple"
        />
        <InputError :message="form.errors.studio_ids" class="mt-2" />
    </div>
    <div>
        <Switcher v-model="form.status" title="Status" />
        <InputError :message="form.errors.status" class="mt-2" />
    </div>
</div>
</template>
<style>
    .pac-container {
        z-index: 2000;
    }
</style>

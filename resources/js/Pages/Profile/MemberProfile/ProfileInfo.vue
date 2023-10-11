<script setup>
import { ref } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faCamera, faUser, faCheck } from "@fortawesome/free-solid-svg-icons";
import { useForm } from "@inertiajs/vue3";
const props = defineProps(["business_settings", "user"]);
const photoPreview = ref(null);
const photoInput = ref(null);

const selectNewPhoto = () => {
    photoInput.value.click();
};

const form = useForm({
    _method: "PUT",
    user_id: props.user.id,
    photo: null,
});

const saveNewPhoto = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }
    form.post(
        route("ss.member.update-photo", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            onSuccess: () => clearPhotoFileInput(),
        }
    );
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
        photoPreview.value = null;
    }
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};
</script>
<template>
    <div class="grow md:border-r-2 border-zinc-300 p-4 basis-1/4">
        <div class="m-auto block text-center">
            <div
                class="rounded-full w-24 h-24 bg-slate-300 text-center m-auto relative"
            >
                <input
                    type="file"
                    class="hidden"
                    ref="photoInput"
                    @change="updatePhotoPreview"
                />
                <img
                    v-if="photoPreview || user?.images[0]?.url"
                    :src="photoPreview || user?.images[0]?.url"
                    class="rounded-full w-full h-full"
                />
                <font-awesome-icon
                    v-else
                    :icon="faUser"
                    class="text-6xl text-stone-50 pt-5"
                />
                <span
                    class="shadow-2xl w-8 h-8 block bg-white rounded-full absolute top-[56px] right-[-8px]"
                >
                    <label
                        v-if="!photoPreview"
                        v-tooltip="'Select New Profile Photo'"
                        class="cursor-pointer"
                        @click.prevent="selectNewPhoto"
                        ><font-awesome-icon
                            :icon="faCamera"
                            class="pt-2 text-blue-800"
                    /></label>
                    <label
                        v-else-if="photoPreview"
                        class="cursor-pointer"
                        v-tooltip="'Save Profile Photo'"
                        @click.prevent="saveNewPhoto"
                        ><font-awesome-icon
                            :icon="faCheck"
                            class="pt-2 text-blue-800"
                    /></label>
                </span>
            </div>
            <h4 class="text-3xl font-extrabold mt-5 mb-5">
                {{ user.full_name }}
            </h4>
            <button
                class="rounded-full bg-[#41bb6c] px-7 py-2 text-white font-semibold"
            >
                Active
            </button>
        </div>
        <div
            class="flex flex-row justify-center w-full py-10"
        >
            <div
                class="border-r-2 border-zinc-300 text-center px-10"
            >
                <h4 class="text-4xl font-semibold">21</h4>
                <h5>Classes</h5>
            </div>
            <div
                class="border-r-2 border-zinc-300 text-center px-10"
            >
                <h4 class="text-4xl font-semibold">238</h4>
                <h5>Check-ins</h5>
            </div>
            <div class="text-center px-10">
                <h4 class="text-4xl font-semibold">10</h4>
                <h5>Credits</h5>
            </div>
        </div>
        <div class="block w-full space-y-8 text-center">
            <h4 class="font-extrabold text-3xl text-center">
                Contact Information
            </h4>
            <p class="text-2xl">
                <span>Email:</span><span>{{ user.email }}</span>
            </p>
            <p class="text-2xl">
                <!-- <span>Phone:</span><span>{{ user.phone }}</span> -->
            </p>
        </div>
    </div>
</template>

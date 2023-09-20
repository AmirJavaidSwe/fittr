<script setup>
import { onMounted, ref, computed, toRef, watchEffect } from "vue";
import { Link, useForm, usePage, router } from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import SideModal from "@/Components/SideModal.vue";
import cloneDeep from "lodash/cloneDeep";
import uniqBy from "lodash/uniqBy";

import LocationCreateForm from "@/Pages/Partner/Location/Form.vue";
import GMCreateForm from "@/Pages/Partner/Users/Form.vue";
import RoleCreateForm from "@/Pages/Roles/Form.vue";
import AmenityCreateForm from "@/Pages/Partner/Amenity/Form.vue";
import StudioCreateForm from "@/Pages/Partner/Studio/Form.vue";
import InstructorCreateForm from "@/Pages/Partner/Instructor/Form.vue";
import ClassTypeCreateForm from "@/Pages/Partner/Classtype/Form.vue";

const props = defineProps({
    showLocationCreateForm: {
        type: Boolean,
        required: false,
        default: false
    },
    showGmCreateForm: {
        type: Boolean,
        required: false,
        default: false
    },
    showAmenityCreateForm: {
        type: Boolean,
        required: false,
        default: false
    },
    showStudioCreateForm: {
        type: Boolean,
        required: false,
        default: false
    },
    showInstructorCreateForm: {
        type: Boolean,
        required: false,
        default: false
    },
    showClassTypeCreateForm: {
        type: Boolean,
        required: false,
        default: false
    },
});

const emit = defineEmits(['closeLocationCreateForm', 'closeGmCreateForm', 'closeAmenityCreateForm', 'closeStudioCreateForm', 'closeInstructorCreateForm', 'closeClassTypeCreateForm']);

const amenities = ref([])
const instructors = ref([])
const locations = ref([])
const roles = ref([])
const statuses = ref([])
const stateTypes = ref([])
const studios = ref([])
const systemModules = ref([])
const users = ref([])
const classtypes = ref([])
const countries = ref([])

const retry = ref(0)

const getResources = () => {
    axios
    .get(route("partner.on-the-fly-resources"), {
        headers: {
            "X-Inertia": true,
            "X-Inertia-Version": usePage().version,
        },
    })
    .then((response) => {
        const data = response.data;
        amenities.value = data.amenities;
        instructors.value = data.instructors;
        locations.value = data.locations;
        roles.value = data.roles;
        statuses.value = data.statuses;
        studios.value = data.studios;
        systemModules.value = data.systemModules;
        users.value = data.users;
        classtypes.value = data.classtypes;
        stateTypes.value = data.stateTypes;
        countries.value = data.countries;
        retry.value = 0;
    }).catch((e) => {
        console.log(e);
        if(retry.value < 3) {
            retry.value++
            getResources();
        } else {
            alert("Something went wrong! Please try again later.");
        }
    });
}
onMounted(() => {
    getResources()
});


const showInstructorCreateFormPropRef = toRef(props, 'showInstructorCreateForm');
const showInstructorCreateForm = ref(showInstructorCreateFormPropRef.value);
watchEffect(() => {
    showInstructorCreateForm.value = showInstructorCreateFormPropRef.value
});

const closeInstructorCreateForm = (data = false) => {
    createInstructorFrom.reset().clearErrors();
    showInstructorCreateForm.value = false;
    emit('closeInstructorCreateForm', data)
};

const createInstructorFrom = useForm({
    name: "",
    email: "",
});

const storeInstructor = () => {
    createInstructorFrom
        .transform((data) => ({
            ...data,
            returnTo: route().current(),
        }))
        .post(route("partner.instructors.store"), {
            preserveScroll: true,
            onSuccess: (res) => {
                closeInstructorCreateForm(res.props.extra.instructor)
                getResources()
            },
        });
};

const showClassTypeCreateFormPropRef = toRef(props, 'showClassTypeCreateForm');
const showClassTypeCreateForm = ref(showClassTypeCreateFormPropRef.value);
watchEffect(() => {
    showClassTypeCreateForm.value = showClassTypeCreateFormPropRef.value
});
const closeClassTypeCreateForm = (data = false) => {
    createClassTypeFrom.reset().clearErrors();
    showClassTypeCreateForm.value = false;
    emit('closeClassTypeCreateForm', data)
};
const createClassTypeFrom = useForm({
    title: null,
    description: null,
    status: false,
});

const storeClassType = () => {
    createClassTypeFrom
        .transform((data) => ({
            ...data,
            returnTo: route().current(),
        }))
        .post(route("partner.classtypes.store"), {
            preserveScroll: true,
            onSuccess: (res) => {
                closeClassTypeCreateForm(res.props.extra.class_type)
                getResources();
            }
        });
};


const showLocationCreateFormPropRef = toRef(props, 'showLocationCreateForm');
const showLocationCreateForm = ref(showLocationCreateFormPropRef.value);
watchEffect(() => {
    showLocationCreateForm.value = showLocationCreateFormPropRef.value
});
const showLocationCreateFormFn = () => {
    createLocationFrom.reset().clearErrors();
    showLocationCreateForm.value = true
};
const closeLocationCreateForm = (data = false) => {
    showLocationCreateForm.value = false;
    createLocationFrom.reset().clearErrors();
    createStudioForm.location_id = (data && data.id) ? data.id : null
    emit('closeLocationCreateForm', data);
};

const createLocationFrom = useForm({
    title: null,
    brief: null,
    manager_id: null,
    address_line_1: null,
    address_line_2: null,
    country_id: null,
    city: null,
    postcode: null,
    map_latitude: null,
    map_longitude: null,
    tel: null,
    email: null,
    image: null,
    amenity_ids: [],
});

const storeLocation = () => {
    createLocationFrom
        .transform((data) => ({
            ...data,
            returnTo: route().current(),
        }))
        .post(route("partner.locations.store"), {
            preserveScroll: true,
            onSuccess: (res) => {
                getResources();
                closeLocationCreateForm(res.props.extra.location);
            }
        });
};

const locationList = computed(() => {
    let newLocationList = locations.value;
    newLocationList.push({
        id: "create_new_location",
        title: "Add New",
    });
    return uniqBy(newLocationList, "id");
});

const rolesList = computed(() => {
    let newRolesList = roles.value;
    newRolesList.push({
        id: "create_new_role",
        title: "Add New",
    });
    return uniqBy(newRolesList, "id");
});

const showGmCreateFormPropRef = toRef(props, 'showGmCreateForm');
const showGmCreateForm = ref(showGmCreateFormPropRef.value);
watchEffect(() => {
    showGmCreateForm.value = showGmCreateFormPropRef.value
});
const showGmCreateFormFn = () => {
    createGmFrom.reset().clearErrors();
    showGmCreateForm.value = true
};

const closeGMCreateForm = (data = false) => {
    createGmFrom.reset().clearErrors();
    showGmCreateForm.value = false;
    createLocationFrom.manager_id = data && data.id ? data.id : ''
    emit('closeGmCreateForm', data)
};
const createGmFrom = useForm({
    name: "",
    email: "",
    password: "",
    is_super: true,
    roles: [],
});

const storeGM = () => {
    createGmFrom
        .transform((data) => ({
            ...data,
            roles: data.is_super ? [] : data.roles,
            is_super: data.is_super,
            is_new: 1,
            returnTo: route().current(),
        }))
        .post(route("partner.users.store"), {
            preserveScroll: true,
            onSuccess: (res) => {
                closeGMCreateForm(res.props.extra.gm);
                getResources();
            }
        });
};

const showRoleCreateModal = ref(false);
const closeRoleCreateModal = () => {
    createRoleFrom.reset().clearErrors();
    showRoleCreateModal.value = false;
    const filtered = createGmFrom.roles.filter((item) => item != 'create_new_role')
    createGmFrom.roles = filtered;
};
const createRoleFrom = useForm({});

const storeRole = ($event) => {
    let title = null;
    let permissions = [];
    if ($event.title) {
        title = $event.title;
    }
    if ($event.permissions) {
        permissions = $event.permissions;
    }
    createRoleFrom
        .transform((data) => ({
            title: title,
            permissions: permissions,
            returnTo: route().current(),
        }))
        .post(route(`${usePage().props.user.source}.roles.store`), {
            preserveScroll: true,
            onSuccess: () => {
                getResources();
                closeRoleCreateModal();
            }
        });
};

const showAmenityCreateFormPropRef = toRef(props, 'showAmenityCreateForm');
const showAmenityCreateForm = ref(showAmenityCreateFormPropRef.value);
watchEffect(() => {
    showAmenityCreateForm.value = showAmenityCreateFormPropRef.value
});

const showAmenityCreateFormFn = () => {
    createAmenityFrom.reset().clearErrors();
    showAmenityCreateForm.value = true;
};

const closeAmenityCreateForm = (data = false) => {
    createAmenityFrom.reset().clearErrors();
    showAmenityCreateForm.value = false;
    const filtered = createLocationFrom.amenity_ids.filter((item) => item != 'create_new_amenity')
    createLocationFrom.amenity_ids = filtered
    if(data && data.id) {
        createLocationFrom.amenity_ids.unshift(data.id)
    }

    emit('closeAmenityCreateForm', data)
};
const createAmenityFrom = useForm({
    title: "",
    status: false,
});

const storeAmenity = () => {
    createAmenityFrom
        .transform((data) => ({
            ...data,
            returnTo: route().current(),
        }))
        .post(route(`partner.amenity.store`), {
            preserveScroll: true,
            onSuccess: (res) => {
                closeAmenityCreateForm(res.props.extra.amenity)
                getResources();
            },
        });
};


const showStudioCreateFormPropRef = toRef(props, 'showStudioCreateForm');
const showStudioCreateForm = ref(showStudioCreateFormPropRef.value);
watchEffect(() => {
    showStudioCreateForm.value = showStudioCreateFormPropRef.value
});


const closeStudioCreateForm = (data = false) => {
    createStudioForm.reset().clearErrors();
    showStudioCreateForm.value = false;
    emit('closeStudioCreateForm', data)
};

const createStudioForm = useForm({
    title: null,
    location_id: null,
});


const storeStudio = () => {
    createStudioForm
        .transform((data) => ({
            ...data,
            returnTo: route().current(),
        }))
        .post(route("partner.studios.store"), {
            preserveScroll: true,
            onSuccess: (res) => {
                closeStudioCreateForm(res.props.extra.studio);
                getResources();
            }
        });
};
</script>

<template>
    <!-- Location Create Modal -->
    <SideModal
        :show="showLocationCreateForm"
        @close="closeLocationCreateForm"
    >
        <template #title> Create new location </template>

        <template #content>
            <LocationCreateForm
                :form="createLocationFrom"
                :users="users"
                :amenities="amenities"
                :countries="countries"
                :studios="[]"
                :editMode="false"
                @create_new_gm="showGmCreateFormFn"
                @create_new_amenity="showAmenityCreateFormFn"
                modal
            />
        </template>
        <template #footer>
            <ButtonLink
                :class="{ 'opacity-25': createLocationFrom.processing }"
                :disabled="createLocationFrom.processing"
                styling="secondary"
                size="default"
                type="submit"
                @click="storeLocation"
            >
                <span>Create</span>
            </ButtonLink>
        </template>
    </SideModal>

    <!-- GM Create Modal -->
    <SideModal :show="showGmCreateForm" @close="closeGMCreateForm">
        <template #title> Create new General Manager </template>

        <template #content>
            <GMCreateForm
                :form="createGmFrom"
                :roles="rolesList"
                @create-new-role="showRoleCreateModal = true"
                :submitted="storeGM"
                modal
            />
        </template>
    </SideModal>
    <!-- Role Create Modal -->
    <SideModal :show="showRoleCreateModal" @close="closeRoleCreateModal">
        <template #title> Create new Role </template>

        <template #content>
            <RoleCreateForm
                :form="createRoleFrom"
                :system-modules="systemModules"
                @submitted="storeRole"
                modal
            />
        </template>
    </SideModal>
    <!-- Amenity Create Modal -->
    <SideModal :show="showAmenityCreateForm" @close="closeAmenityCreateForm">
        <template #title> Create new Amenity </template>

        <template #content>
            <AmenityCreateForm
                :form="createAmenityFrom"
                :submitted="storeAmenity"
                modal
            />
        </template>
    </SideModal>


    <!-- Studio Create Modal -->
    <SideModal :show="showStudioCreateForm" @close="closeStudioCreateForm">
        <template #title> Create new studio </template>

        <template #content>
            <StudioCreateForm
                :form="createStudioForm"
                :locations="locationList"
                @create-new-location="showLocationCreateFormFn"
                :submitted="storeStudio"
                modal
            />
        </template>
    </SideModal>

    <!-- Instructor Create Modal -->
    <SideModal :show="showInstructorCreateForm" @close="closeInstructorCreateForm">
        <template #title> Create new Instructor </template>

        <template #content>
            <InstructorCreateForm
                :form="createInstructorFrom"
                :submitted="storeInstructor"
                modal
            />
        </template>
    </SideModal>

    <!-- Class Type Create Modal -->
    <SideModal
        :show="showClassTypeCreateForm"
        @close="closeClassTypeCreateForm"
    >
        <template #title> Create new Class Type </template>

        <template #content>
            <ClassTypeCreateForm
                :form="createClassTypeFrom"
                :submitted="storeClassType"
                :statuses="stateTypes"
                modal
            />
        </template>
    </SideModal>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Form from "./Form.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import SideModal from "@/Components/SideModal.vue";
import cloneDeep from "lodash/cloneDeep";
import uniqBy from "lodash/uniqBy";
import OnTheFlyResourceCreate from "@/Components/OnTheFlyResourceCreate.vue";
import { hideAllPoppers } from 'floating-vue';
import Avatar from "@/Components/Avatar.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    locations: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
    users: Array,
    amenities: Array,
    countries: Array,
    studios: Array,
    systemModules: Array,
    roles: Array,
});

const form_search = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const form_item = useForm({
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
    amenity_ids: [],
    image: null,
    uploaded_images: [],
    status: true,
    studio_ids: [],
});

const runSearch = () => {
    form_search.get(route("partner.locations.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const setOrdering = (col) => {
    //reverse same col order
    if (form_search.order_by == col) {
        form_search.order_dir = form_search.order_dir == "asc" ? "desc" : "asc";
    }
    form_search.order_by = col;
    runSearch();
};

const setPerPage = (n) => {
    form_search.per_page = n;
    runSearch();
};

// form.search getter only;
watch(() => form_search.search, runSearch);

//delete confiramtion modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    hideAllPoppers();
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form_item.delete(
        route("partner.locations.destroy", { id: itemIdDeleting.value }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                itemDeleting.value = false;
                itemIdDeleting.value = null;
            },
        }
    );
};

// create new location modal
const modal = ref(false);
const editMode = ref(false);
const editId = ref(null);

const saveForm = () => {
    if (editMode.value) {
        form_item
            .transform((data) => ({
                ...data,
                _method: "put",
            }))
            .post(route("partner.locations.update", { id: editId.value }), {
                preserveScroll: true,
                onSuccess: () => {
                    modal.value = false;
                    editId.value = null;
                    form_item.reset().clearErrors();
                },
            });
    } else {
        form_item
            .post(route("partner.locations.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    modal.value = false;
                    form_item.reset().clearErrors();
                },
            });
    }
};

const showCreateModal = () => {
    hideAllPoppers();
    form_item.reset().clearErrors();
    editMode.value = false;
    modal.value = true;
    editId.value = null;
};

const studio_options = ref(props.studios);
const showEditModal = (data) => {
    hideAllPoppers();
    form_item.reset().clearErrors();

    data = Object.assign({}, data);

    form_item.title = data.title;
    form_item.brief = data.brief;
    form_item.manager_id = data.manager?.id;
    form_item.address_line_1 = data.address_line_1;
    form_item.address_line_2 = data.address_line_2;
    form_item.country_id = data.country_id;
    form_item.city = data.city;
    form_item.postcode = data.postcode;
    form_item.map_latitude = data.map_latitude;
    form_item.map_longitude = data.map_longitude;
    form_item.tel = data.tel;
    form_item.email = data.email;
    form_item.amenity_ids = data.amenities.map((item) => item.id);
    form_item.uploaded_images = [...data.images];
    form_item.status = !!data.status;
    form_item.studio_ids = data.studios.map((item) => item.id);

    editId.value = data.id;
    editMode.value = true;
    modal.value = true;

    //list of studio options should include currently assigned to location + orphans from props
    studio_options.value = props.studios.concat(...data.studios.map((studio) => ({value: studio.id, label: studio.title})));
};

const fileRemoveForm = useForm({
    image_id: "",
});

const removeUploadedFile = (id) => {
    fileRemoveForm.image_id = id;
    fileRemoveForm.delete(
        route("partner.locations.delete-image", editId.value),
        {
            onSuccess: () => {
                fileRemoveForm.reset();
                showEditModal(
                    props.locations?.data.find(
                        (item) => item.id == editId.value
                    )
                );
            },
        }
    );
};

const showGMCreateForm = ref(false);
const showAmenityCreateForm = ref(false);
const showStudioCreateForm = ref(false);
const closeGMCreateForm = (data = false) => {
    form_item.manager_id = data && data.id ? data.id : '';
    showGMCreateForm.value = false;
};
const closeStudioCreateForm = (data = false) => {
    form_item.studio_ids = form_item.studio_ids.filter( (item) => item != "create_new_studio" );
    if(data && data.id) {
        studio_options.value.push({value: data.id, label: data.title});
        form_item.studio_ids.unshift(data.id)
    }
    showStudioCreateForm.value = false;
};

const closeAmenityCreateForm = (data = false) => {
    const amenity_ids = form_item.amenity_ids.filter(
        (item) => item != "create_new_amenity"
    );
    form_item.amenity_ids = amenity_ids;
    if(data && data.id) {
        form_item.amenity_ids.unshift(data.id);
    }
    showAmenityCreateForm.value = false;
};
</script>

<template>
    <DataTableLayout :disable-button="true" :disable-search="disableSearch">
        <template #button>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="showCreateModal"
            >
                Create location
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink
                styling="secondary"
                size="default"
                class="hidden"
                :href="route('partner.locations.create')"
                type="primary"
            >
                Create location (direct)
            </ButtonLink> -->
        </template>

        <template #search>
            <Search
                v-model="form_search.search"
                :disable-search="disableSearch"
                @reset="form_search.search = null"
                :noFilter="true"
            />
        </template>

        <template #tableHead>
            <TableHead title="Image" />
            <TableHead
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'title'"
            />
            <TableHead title="Studios" />
            <TableHead
                title="General Manager"
                @click="setOrdering('manager_id')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'manager_id'"
            />
            <TableHead
                title="Created At"
                @click="setOrdering('created_at')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'created_at'"
            />
            <TableHead
                title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'updated_at'"
            />
            <TableHead title="Action" class="justify-end flex" />
        </template>

        <template #tableData>
            <tr v-for="(location, index) in locations.data">
                <TableData>
                    <div v-if="location.images.length" class="h-10">
                        <img :src="location.images[0].url" alt="image" class="h-full">
                    </div>
                </TableData>
                <TableData>
                    <ButtonLink :href="route('partner.locations.show', location)">
                        {{ location.title }}
                    </ButtonLink>
                </TableData>
                <TableData>
                    <ButtonLink v-for="studio in location.studios" :key="studio.id" :href="route('partner.studios.show', studio)" size="small">
                        {{ studio.title }}
                    </ButtonLink>
                </TableData>
                <TableData>
                    <div class="flex items-center gap-2">
                        {{location.manager?.full_name}}
                        <Avatar v-if="location.manager" size="small" :initials="location.manager.initials" :imageUrl="location.manager.profile_photo_url" />
                    </div>
                </TableData>
                <TableData>
                    <DateValue
                        :date="
                            DateTime.fromISO(location.created_at)
                                .setZone(business_settings.timezone)
                                .toFormat(business_settings.date_format.format_js)
                        "
                    />
                </TableData>
                <TableData>
                    <DateValue :date="DateTime.fromISO(location.updated_at).toRelative()" />
                </TableData>
                <TableData class="text-right">
                    <VDropdown placement="bottom-end">
                        <button><ActionsIcon /></button>
                        <template #popper>
                            <div class="p-2 w-40 space-y-4">
                                <ButtonLink
                                    styling="blank"
                                    size="small"
                                    class="w-full flex justify-between hover:bg-gray-100"
                                    @click="showEditModal(location)"
                                    >
                                    <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Edit </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="transparent"
                                    size="small"
                                    class="w-full flex justify-between text-danger-500 hover:text-danger-700 hover:bg-gray-100"
                                    @click="confirmDeletion(location.id)"
                                    >
                                    <DeleteIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Delete </span>
                                </ButtonLink>
                            </div>
                        </template>
                    </VDropdown>
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="locations.links"
                :to="locations.to"
                :from="locations.from"
                :total="locations.total"
                @pp_changed="setPerPage"
            />
        </template>
    </DataTableLayout>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title> Confirmation required </template>

        <template #content>
            Are you sure you would like to delete this?
        </template>

        <template #footer>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="itemDeleting = null"
                >Cancel</ButtonLink
            >
            <ButtonLink
                styling="danger"
                size="default"
                class="ml-3"
                :class="{ 'opacity-25': form_item.processing }"
                :disabled="form_item.processing"
                @click="deleteItem"
                >Delete</ButtonLink
            >
        </template>
    </ConfirmationModal>

    <!-- Create/Edit Location Modal -->
    <SideModal :show="modal" @close="modal = false">
        <template #title>
            {{ editMode ? "Edit" : "Create" }} Location
        </template>

        <template #content>
            <Form
                :form="form_item"
                :users="users"
                :amenities="amenities"
                :countries="countries"
                :studios="studio_options"
                @remove_uploaded_file="removeUploadedFile"
                @create_new_gm="showGMCreateForm = true"
                @create_new_amenity="showAmenityCreateForm = true"
                @createNewStudio="showStudioCreateForm = true"
                modal
            />
        </template>
        <template #footer>
            <ButtonLink
                :class="{ 'opacity-25': form_item.processing }"
                :disabled="form_item.processing"
                styling="secondary"
                size="default"
                type="submit"
                @click="saveForm"
            >
                <span v-if="!editMode">Create</span>
                <span v-else>Save changes</span>
            </ButtonLink>
        </template>
    </SideModal>

    <OnTheFlyResourceCreate
        :show-gm-create-form="showGMCreateForm"
        :show-amenity-create-form="showAmenityCreateForm"
        :show-studio-create-form="showStudioCreateForm"
        @close-gm-create-form="closeGMCreateForm"
        @close-amenity-create-form="closeAmenityCreateForm"
        @close-studio-create-form="closeStudioCreateForm"
    />
</template>

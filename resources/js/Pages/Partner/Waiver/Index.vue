<script setup>
import { ref, watch, computed } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { WaiverHelpers } from "./WaiverHelpers/Index.js";
// import Section from '@/Components/Section.vue';

import SectionTitle from "@/Components/SectionTitle.vue";
import SearchFilter from "@/Components/SearchFilter.vue";
import Pagination from "@/Components/Pagination.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { DateTime } from "luxon";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import Avatar from "@/Components/Avatar.vue";
import Search from "@/Components/DataTable/Search.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import SideModal from "@/Components/SideModal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import uniqBy from "lodash/uniqBy";
import RoleCreateForm from "@/Pages/Roles/Form.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import StatusLabel from "@/Components/StatusLabel.vue";
import Modal from "@/Components/Modal.vue";
import CardBasic from "@/Components/CardBasic.vue";

import {
    faPencil,
    faChevronRight,
    faPlus,
    faEye,
    faCog,
} from "@fortawesome/free-solid-svg-icons";
import find from "lodash/find";

const props = defineProps({
    waivers: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
    systemModules: Array,
    business_settings: Object,
});
const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const helpers = WaiverHelpers();

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(route("partner.waivers.destroy", { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

const setOrdering = (col) => {
    //reverse same col order
    if (form.order_by == col) {
        form.order_dir = form.order_dir == "asc" ? "desc" : "asc";
    }
    form.order_by = col;
    runSearch();
};

const runSearch = () => {
    form.get(route(`partner.waivers.index`), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);

const waiverShowPlaces = ref([
    {
        label: "On Signup",
        value: "sign-up"
    },
    {
        label: "Upon Adding a Family Member",
        value: "family-add"
    },
    {
        label: "On Checkout",
        value: "checkout"
    }
]);

const singleWaiver = ref([])
const singleQuestionsModal = ref(false)

const showQuestion = (id) => {
    const obj = find(props.waivers, (o) => {
        return o.id === id
    })
    singleWaiver.value = obj
    singleQuestionsModal.value = true
}

</script>
<template>
    <data-table-layout :disableButton="true">
        <template #search>
            <!-- <Search
                :noFilter="true"
                v-model="form.search"
                @reset="form.search = null"
            /> -->
        </template>

            <template #button>
                <ButtonLink class="text-right align-right"
                v-can="{
                    module: 'users',
                    roles: $page.props.user.user_roles,
                    permission: 'create',
                    user: $page.props.user,
                }"
                styling="secondary"
                size="default"
                :href="route('partner.waivers.create')"
                >
                Create new
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink v-can="{
                module: 'users',
                roles: $page.props.user.user_roles,
                permission: 'create',
                user: $page.props.user,
            }" :href="route(`partner.waivers.create`)" type="primary" styling="secondary" size="default">Add new
            <font-awesome-icon class="ml-2" :icon="faPlus" /></ButtonLink> -->
        </template>

        <template #tableHead>
            <table-head
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <table-head
                title="Show At"
                @click="setOrdering('show_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'show_at'"
            />
            <table-head title="Description" />
            <table-head title="Questions" />
        </template>

        <template #tableData>
            <tr v-for="(obj, index) in waivers">
                <table-data>{{ obj.title }}</table-data>
                <table-data>{{ helpers.getShowAtValue(obj.show_at).label }}</table-data>
                <table-data>
                    <span
                            v-if="obj.description.length > 25"
                            v-tooltip="obj.description"
                        >
                            {{ obj.description.substring(0, 25) }}...
                        </span>
                        <span v-else>
                            {{ obj.description }}
                        </span>
                </table-data>
                <table-data>
                    <span @click="showQuestion(obj.id)" class="cursor-pointer">Cick to Show</span>
                </table-data>
            </tr>

        </template>

        <template #pagination>
            <!-- <pagination
                :links="waivers.links"
                :to="waivers.to"
                :from="waivers.from"
                :total="waivers.total"
                @pp_changed="runSearch"
            /> -->
        </template>
    </data-table-layout>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title> Confirmation required </template>

        <template #content>
            Are you sure you would like to delete this?
        </template>

        <template #footer>
            <ButtonLink
                size="default"
                styling="default"
                @click="itemDeleting = null"
            >
                Cancel
            </ButtonLink>

            <ButtonLink
                size="default"
                styling="danger"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteItem"
            >
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>

    <Modal :show="singleQuestionsModal" :sideModalOpened="false">
        <CardBasic>
            <template #header>
                <div class="flex justify-between items-center">
                    <div class="text-md mx-auto">Waiver Questions</div>
                    <div>
                        <CloseModal @click="singleQuestionsModal = false" v-tooltip="'Cancel and Close'" />
                    </div>
                </div>
            </template>
            <template #default>
                {{ singleWaiver }}
            </template>

        </CardBasic>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Form from "./Form.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    business_seetings: Object,
    classtypes: Object,
    pack_types: Object,    
    periods: Object,
    price_types: Object,
    packs: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const form_fields = {
    type: null,
    title: null,
    sub_title: null,
    description: null,
    is_active: false,
    is_restricted: false,
    is_unlimited: false,
    is_fap: false,
    is_private: false,
    restrictions: null,
    private_url: null,
    active_from: null,
    active_to: null,
};

const form_item = useForm(form_fields);

const formPrice = useForm({
    type: 'one_time',
    is_active: false,
    price: null,
    sessions: null,
    is_expiring: false,
    expiration: null,
    expiration_period: null,
    interval: null,
    interval_count: null,
    is_ongoing: true,
    fixed_count: null,
    is_renewable: true,
    is_intro: false,
});

const runSearch = () => {
    form.get(route('partner.packs.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const setPerPage = (n) => {
    form.per_page = n;
    runSearch();
};

// form.search getter only;
watch(() => form.search, runSearch);

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(route('partner.packs.destroy', { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

//duplicate confirmation modal:
const itemDuplicating = ref(false);
const itemIdDuplicating = ref(null);
const confirmDuplication = (id) => {
    itemIdDuplicating.value = id;
    itemDuplicating.value = true;
};
const duplicateItem = () => {
    form.post(route('partner.packs.duplicate', { id: itemIdDuplicating.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDuplicating.value = false;
            itemIdDuplicating.value = null;
        },
    });
};

//create modal:
const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    form_item.reset().clearErrors();
};
//create item from modal
const storeItem = () => {
    form_item.post(route('partner.packs.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form_item.reset();
            closeCreateModal();
        }
    });
};

//edit modal:
const editModalOpened = ref(false);
const editedId = ref(null);
const showEditModal = (pack) => {
    //set pack edited to form
    for (const property in form_fields) {
        form_item[property] = pack[property];
    }

    form_item['prices'] = pack.prices;
    editModalOpened.value = true;
    editedId.value = pack.id;
};
const closeEditModal = () => {
    editModalOpened.value = false;
    editedId.value = null;
    form_item.reset().clearErrors();
};
//save item from modal:
const updateItem = () => {
    form_item.put(route('partner.packs.update', {id: editedId.value}), {
        preserveScroll: true,
        onSuccess: () => {
            form_item.reset();
            closeEditModal();
        }
    });
};

const storePrice = () => {
    formPrice.post(route('partner.packs.price.store', editedId.value), {
        preserveScroll: true,
        onSuccess: () => {
            //update data in the modal
            showEditModal(props.packs.data.find(element => element.id == editedId.value));
        }
    });
};
const editPrice = (action, id) => {
    useForm({'action': action}).put(route('partner.packs.price.update', { pack: editedId.value, price: id } ), {
        preserveScroll: true,
        onSuccess: () => {
            //update data in the modal
            showEditModal(props.packs.data.find(element => element.id == editedId.value));
        }
    });
};
</script>
<template>
    <data-table-layout
        :disableButton="true"
       >
        <template #button>
            <div class="flex gap-2">
                <SecondaryButton @click="showCreateModal = true">
                    Create new (modal)
                </SecondaryButton>
                <ButtonLink :href="route('partner.packs.create')" type="primary">
                    Create new (direct)
                </ButtonLink>
            </div>
        </template>

        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @pp_changed="setPerPage"
                />
        </template>

        <template #tableHead>
            <table-head title="Id"/>
            <table-head title="Title"/>
            <table-head title="Prices"/>
            <table-head title="Type"/>
            <table-head title="Status"/>
            <table-head title="Created At"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="pack in packs.data" >
                <table-data :title="pack.id"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.packs.show', pack)"
                          >
                           {{ pack.title.length > 50 ? pack.title.substring(0, 50) + '...' : pack.title }} 
                    </Link>
                </table-data>
                <table-data>
                    <div v-if="pack.prices">
                        <div v-for="price in pack.prices">
                            {{price.price}} {{price.currency.toUpperCase()}} {{price_types.find(({value}) => value === price.type)?.label}} {{price.is_active ? 'ON' : 'OFF'}}
                        </div>
                    </div>
                </table-data>
                <table-data :title="pack_types.find(({value}) => value === pack.type)?.label "/>
                <table-data :title="pack.is_active ? 'ON' : 'OFF' "/>
                <table-data :title="DateTime.fromISO(pack.created_at).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js)"/>
                <table-data :title="DateTime.fromISO(pack.updated_at).toRelative()"/>
                <table-data>
                    <div class="flex gap-4">
                        <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                            :href="route('partner.packs.edit', pack)">
                            Edit
                        </Link>
                        <a @click.stop="showEditModal(pack)" class="cursor-pointer">Edit2</a>
                        <button class="block text-green-500" @click="confirmDuplication(pack.id)">
                            Duplicate
                        </button>
                        <button class="block text-red-500" @click="confirmDeletion(pack.id)">
                            Delete
                        </button>
                    </div>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="packs.links"/>
            <p class="p-2 text-xs">Viewing {{packs.from}} - {{packs.to}} of {{packs.total}} results</p>
        </template>    
    </data-table-layout>

    <!-- Create new Modal -->
    <DialogModal :show="showCreateModal" @close="closeCreateModal">
        <template #title>
            Create new Class pack
        </template>

        <template #content>
            <Form :form="form_item"
                :isNew="true"
                :formPrice="formPrice"
                :pack_types="pack_types"
                :periods="periods"
                :classtypes="classtypes"
                :default_currency="business_seetings?.default_currency"
                :submitted="storeItem"/>
        </template>
    </DialogModal>

    <!-- Edit Modal -->
    <DialogModal :show="editModalOpened" @close="closeEditModal">
        <template #title>
            Edit Membership pack
        </template>

        <template #content>
            <Form :form="form_item"
                :formPrice="formPrice"
                :pack_types="pack_types"
                :periods="periods"
                :price_types="price_types"
                :classtypes="classtypes"
                :default_currency="business_seetings?.default_currency"
                :prices="form_item.prices"
                :submitted="updateItem"
                :savePrice="storePrice"
                :editPrice="editPrice"
            />
        </template>
    </DialogModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title>
            Confirmation required
        </template>

        <template #content>
            Are you sure you would like to delete this?
        </template>

        <template #footer>
            <SecondaryButton @click="itemDeleting = false">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteItem"
            >
                Delete
            </DangerButton>
        </template>
    </ConfirmationModal>

    <!-- Duplicate Confirmation Modal -->
    <ConfirmationModal :show="itemDuplicating" @close="itemDuplicating = false">
        <template #title>
            Confirmation required
        </template>

        <template #content>
            Once confirmed, a copy of this pack will be created.<br>
            Duplicated pack will be inactive and hidden to public.<br>
            You will need to update new pack pricing to suit your needs.<br>
        </template>

        <template #footer>
            <SecondaryButton @click="itemDuplicating = false">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="duplicateItem"
            >
                Create duplicate
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
<script setup>
import { ref, watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { faPowerOff, faLocationPinLock } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    pack_types: Object,
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

const runSearch = () => {
    form.get(route("partner.packs.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
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
    form.delete(route("partner.packs.destroy", { id: itemIdDeleting.value }), {
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
    form.post(
        route("partner.packs.duplicate", { id: itemIdDuplicating.value }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                itemDuplicating.value = false;
                itemIdDuplicating.value = null;
            },
        }
    );
};
</script>
<template>
    <data-table-layout :disableButton="true">
        <template #button>
            <div class="flex gap-2">
                <ButtonLink
                    :href="route('partner.packs.create')"
                    styling="primary"
                    size="default"
                >
                    Create new
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
            <TableHead title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <TableHead title="Prices" />
            <TableHead title="Type"
                @click="setOrdering('type')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'type'"
            />
            <TableHead title="Status"  
                @click="setOrdering('is_active')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'is_active'"
            />
            <TableHead
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            >
                <div>
                    Created
                    <span v-tooltip="DateTime.now().setZone(business_settings.timezone).toFormat('z')">
                        ({{ DateTime.now().setZone(business_settings.timezone).toFormat('ZZZZ')}})
                    </span>
                </div>
            </TableHead>
            <TableHead title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <TableHead title="Action" />
        </template>

        <template #tableData>
            <tr v-for="pack in packs.data">
                <TableData>
                    <ButtonLink :href="route('partner.packs.show', pack)">
                        {{
                            pack.title.length > 50
                                ? pack.title.substring(0, 50) + "..."
                                : pack.title
                        }}
                    </ButtonLink>
                </TableData>
                <TableData>
                    <div v-if="pack.pack_prices">
                        <div v-for="price in pack.pack_prices" class="space-x-1">
                            <span class="font-bold" :title="price.currency.toUpperCase()">{{price.price_formatted}}</span>
                            <span>
                                {{price_types.find(({value}) => value === price.type)?.label}}
                            </span>
                            <span>
                                <font-awesome-icon 
                                    :icon="faPowerOff"
                                    :class="[price.is_active  ? 'text-primary-500' : 'text-danger-200']"
                                     v-tooltip="price.is_active ? 'Active' : 'Inactive'"
                                />
                            </span>
                            <span v-if="price.locations.length">
                                <font-awesome-icon :icon="faLocationPinLock" v-tooltip="'Location restricted'" />
                            </span>
                        </div>
                    </div>
                </TableData>
                <TableData :title="pack_types.find(({ value }) => value === pack.type)?.label" />
                <TableData :title="pack.is_active ? 'ON' : 'OFF'" />
                <TableData>
                    <DateValue :date="DateTime.fromISO(pack.created_at)
                        .setZone(business_settings.timezone)
                        .toFormat(business_settings.date_format.format_js + ' ' + business_settings.time_format.format_js)" />
                </TableData>
                <TableData :title="DateTime.fromISO(pack.updated_at).toRelative()"/>
                <TableData>
                    <div class="flex gap-4">
                        <ButtonLink :href="route('partner.packs.edit', pack)">
                            Edit
                        </ButtonLink>
                        <ButtonLink @click="confirmDuplication(pack.id)">
                            Duplicate
                        </ButtonLink>
                        <ButtonLink
                            styling="danger"
                            size="small"
                            @click="confirmDeletion(pack.id)"
                        >
                            Delete
                        </ButtonLink>
                    </div>
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <pagination :links="packs.links" />
            <p class="p-2 text-xs">
                Viewing {{ packs.from }} - {{ packs.to }} of
                {{ packs.total }} results
            </p>
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
                styling="default"
                size="default"
                @click="itemDeleting = false"
            >
                Cancel
            </ButtonLink>

            <ButtonLink
                styling="danger"
                size="default"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteItem"
            >
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>

    <!-- Duplicate Confirmation Modal -->
    <ConfirmationModal :show="itemDuplicating" @close="itemDuplicating = false">
        <template #title> Confirmation required </template>

        <template #content>
            Once confirmed, a copy of this pack will be created.<br />
            Duplicated pack will be inactive and hidden to public.<br />
            You will need to update new pack pricing to suit your needs.<br />
        </template>

        <template #footer>
            <ButtonLink
                styling="default"
                size="default"
                @click="itemDuplicating = false"
            >
                Cancel
            </ButtonLink>

            <ButtonLink
                styling="danger"
                size="default"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="duplicateItem"
            >
                Create duplicate
            </ButtonLink>
        </template>
    </ConfirmationModal>
</template>

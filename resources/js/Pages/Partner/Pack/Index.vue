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
import DialogModal from "@/Components/DialogModal.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
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

const runSearch = () => {
    form.get(route("partner.packs.index"), {
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
            <!-- <table-head title="Id"/> -->
            <table-head title="Title" />
            <table-head title="Prices" />
            <table-head title="Type" />
            <table-head title="Status" />
            <table-head title="Created At" />
            <table-head title="Updated At" />
            <table-head title="Action" />
        </template>

        <template #tableData>
            <tr v-for="pack in packs.data">
                <!-- <table-data :title="pack.id"/> -->
                <table-data>
                    <ButtonLink :href="route('partner.packs.show', pack)">
                        {{
                            pack.title.length > 50
                                ? pack.title.substring(0, 50) + "..."
                                : pack.title
                        }}
                    </ButtonLink>
                </table-data>
                <table-data>
                    <div v-if="pack.prices">
                        <div v-for="price in pack.prices">
                            <span
                                class="font-bold"
                                :title="price.currency.toUpperCase()"
                                >{{ price.price_formatted }}</span
                            >
                            {{
                                price_types.find(
                                    ({ value }) => value === price.type
                                )?.label
                            }}
                            {{ price.is_active ? "ON" : "OFF" }}
                        </div>
                    </div>
                </table-data>
                <table-data
                    :title="
                        pack_types.find(({ value }) => value === pack.type)
                            ?.label
                    "
                />
                <table-data :title="pack.is_active ? 'ON' : 'OFF'" />
                <table-data
                    :title="
                        DateTime.fromISO(pack.created_at)
                            .setZone(business_seetings.timezone)
                            .toFormat(business_seetings.date_format.format_js)
                    "
                />
                <table-data
                    :title="DateTime.fromISO(pack.updated_at).toRelative()"
                />
                <table-data>
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
                </table-data>
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

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import PackCard from "@/Pages/Partner/Pack/PackCard.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import draggable from 'vuedraggable';

const props = defineProps({
    business_settings: Object,
    price_types: Object,
    packs: Object,
});

// draggable sorting order
const sortPacks = (e) => {
    axios.post(route("partner.packs.sort"), sorted.value);
};
const sorted = computed(() => {
  return Object.keys(props.packs).map((pack_group) => {
        return props.packs[pack_group].packs.map((pack, index) => {
            return {id: pack.id, ordering: index};
        });
    }).flat();
})

const form = useForm({});

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

//toggle pack status
const toggleActive = (id) => {
    form.post(
        route("partner.packs.toggle", { id: id }),
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};
</script>
<template>
    <div class="flex justify-end gap-2">
        <ButtonLink
            :href="route('partner.packs.create')"
            styling="primary"
            size="small"
        >
            Create new
        </ButtonLink>
    </div>
    <div class="space-y-8">
        <div v-for="group in packs" :key="group.type">
            <div class="flex gap-4 items-center">
                <span class="font-medium text-3xl">{{group.label.label_plural}}</span>
                <span class="bg-white border flex h-8 items-center justify-center rounded-full text-sm w-8">{{group.pack_count}}</span>
            </div>
            <div class="border-b-2 text-grey mb-4" v-tooltip.left="'PackType.description'">{{group.label.description}}</div>
                <transition-group>
                    <draggable
                        item-key="id"
                        key="transition-group-key"
                        :list="group.packs"
                        :animation="200"
                        ghostClass="opacity-50"
                        @end="sortPacks"
                        class="flex flex-wrap gap-4"
                        >
                        <template #item="{element}">
                            <PackCard
                                :pack="element"
                                :price_types="price_types"
                                :business_settings="business_settings"
                                @copy=confirmDuplication
                                @delete=confirmDeletion
                                @toggle=toggleActive
                                class="bg-white rounded-md border-t-8 shadow w-80"
                            />
                        </template>
                    </draggable>
                </transition-group>
        </div>
    </div>

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

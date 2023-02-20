<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import Section from '@/Components/Section.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import { faCoffee, faHeart} from '@fortawesome/free-solid-svg-icons';

const showModal = ref(false);
const showActionMessage = ref(false);
const closeModal = () => {
    showModal.value = false;
};
</script>

<template>
    <Section>
        <SectionTitle>
            <template #title>
                <div class="font-bold text-gray-500 border-b mb-4">Headings</div>
            </template>
        </SectionTitle>
        <h1 class="font-bold text-2xl">H1 24px</h1>
        <h2 class="font-bold text-base">H2 16px</h2>
        <h3 class="font-bold text-sm">H3 14px</h3>
        <h4 class="">H4</h4>
        <h5 class="">H5</h5>
        <h6 class="">H6</h6>
    </Section>

    <Section>
        <SectionTitle>
            <template #title>
                <div class="font-bold text-gray-500 border-b mb-4">Buttons and actions</div>
            </template>
        </SectionTitle>

        <div class="flex gap-4 items-center">
            <Link href="#" @click="$emit('someEvent')">Link</Link>
            <Link :href="route('logout')" method="post" as="button" type="button">Form submit - Log out</Link>
            <SecondaryButton @click="showActionMessage = !showActionMessage">Secondary Button</SecondaryButton>
            <DangerButton @click="showActionMessage = !showActionMessage">Danger Button</DangerButton>

            <PrimaryButton type="button" @click="showModal = true">
                Modal Dialog
            </PrimaryButton>
            <DialogModal :show="showModal" @close="showModal = false">
                <template #title>
                    Modal title
                </template>

                <template #content>
                    content
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <PrimaryButton class="ml-3" @click="closeModal">Confirm</PrimaryButton>
                </template>
            </DialogModal>
        </div>
        <ActionMessage class="mr-3" :on="showActionMessage">
            Action message.
        </ActionMessage>
    </Section>

    <Section>
        <SectionTitle>
            <template #title>
                <div class="font-bold text-gray-500 border-b mb-4">Icons (svg)</div>
            </template>
            <template #description>
                <a href="https://fontawesome.com/search?o=r&s=solid&f=classic" target="_blank" rel="noopener noreferrer">https://fontawesome.com/search?o=r&s=solid&f=classic</a>
            </template>
        </SectionTitle>

        <div class="flex gap-4 items-center">
            <font-awesome-icon :icon="faCoffee" />
            <font-awesome-icon :icon="faCoffee" style="color: #7e4141" />
            <font-awesome-icon :icon="faCoffee" size="xs" />
            <font-awesome-icon :icon="faCoffee" size="sm" />
            <font-awesome-icon :icon="faCoffee" size="lg" />
            <font-awesome-icon :icon="faCoffee" size="xl" />
            <font-awesome-icon :icon="faCoffee" size="2xl" />

            <font-awesome-icon :icon="faCoffee" size="1x" />
            <font-awesome-icon :icon="faCoffee" size="2x" />
            <font-awesome-icon :icon="faCoffee" size="3x" />
            <font-awesome-icon :icon="faCoffee" size="4x" />
            <font-awesome-icon :icon="faCoffee" size="5x" />
            <font-awesome-icon :icon="faCoffee" size="6x" />

            <font-awesome-icon :icon="faCoffee" rotation="90" />
            <font-awesome-icon :icon="faCoffee" rotation="270" />
            <font-awesome-icon :icon="faCoffee" flip="horizontal" border />

            <font-awesome-icon :icon="faHeart" beat border />
        </div>
    </Section>

    <SectionBorder />

    <Section>
        <SectionTitle>
            <template #title>
                Section component (default) - this is SectionTitle, title slot
            </template>
            <template #description>
                SectionTitle, description slot
            </template>
            <template #aside>
                SectionTitle, aside slot
            </template>
        </SectionTitle>
    </Section>

    <Section bg="bg-transparent">
        <SectionTitle>
            <template #title>
                Section component (transparent, no shadow) - this is SectionTitle, title slot
            </template>
            <template #description>
                SectionTitle, description slot
            </template>
            <template #aside>
                SectionTitle, aside slot
            </template>
        </SectionTitle>
    </Section>

    <Section bg="bg-green-300 shadow-xl">
        <SectionTitle>
            <template #title>
                Section component (bg, + shadow) - this is SectionTitle, title slot
            </template>
            <template #description>
                SectionTitle, description slot
            </template>
            <template #aside>
                SectionTitle, aside slot
            </template>
        </SectionTitle>
    </Section>

    <ActionSection>
            <template #title>
                ActionSection, title slot
            </template>

            <template #description>
                ActionSection, description slot
            </template>

            <template #content>
                ActionSection, content slot
            </template>
    </ActionSection>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import ButtonLink from '@/Components/ButtonLink.vue';
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
const showAlert = () => {
    alert("button clicked");
};
</script>

<template>
    <Section class="bg-white p-2 mb-4">
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

    <Section class="bg-white p-2 mb-4">
        <SectionTitle>
            <template #title>
                <div class="font-bold text-gray-500 border-b mb-4">Buttons and actions</div>
            </template>
        </SectionTitle>

        <div>
            <ButtonLink styling="primary" size="small" @click="showActionMessage = !showActionMessage">Action message</ButtonLink>
            <ActionMessage class="mr-3" :on="showActionMessage">
                Action message.
            </ActionMessage>

            <!-- buttons demo -->
            <div class="space-y-4 mb-8">
                <h2 class="font-bold text-lg">
                    @\Components\ButtonLink.vue - single component to replace all
                    buttons:
                </h2>
                <p>
                    This is a
                    <ButtonLink :href="route('partner.dashboard')">link</ButtonLink>
                    inside paragraph.
                </p>
                <div>
                    This is a
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="blank"
                        as="button"
                        >blank button</ButtonLink
                    >
                    with href, GET method
                </div>
                <div>
                    This is another blank button
                    <ButtonLink
                        :href="route('logout')"
                        styling="blank"
                        as="button"
                        method="post"
                        >logout</ButtonLink
                    >
                    with href, POST method
                </div>
                <div>
                    This is blank button with sizes (padding):
                    <ButtonLink href="#" styling="blank" size="small"
                        >Small button</ButtonLink
                    >
                    <ButtonLink href="#" styling="blank" size="default"
                        >Default button</ButtonLink
                    >
                    <ButtonLink href="#" styling="blank" size="medium"
                        >Medium button</ButtonLink
                    >
                    <ButtonLink href="#" styling="blank" size="large"
                        >Large button</ButtonLink
                    >
                </div>
                <div class="flex flex-wrap items-start gap-4">
                    <span class="flex-grow"
                        >This is a primary &lt;button&gt; with link:
                    </span>
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="primary"
                        size="small"
                        as="button"
                        >Small button</ButtonLink
                    >
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="primary"
                        size="default"
                        as="button"
                        >Default button</ButtonLink
                    >
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="primary"
                        size="medium"
                        as="button"
                        >Medium button</ButtonLink
                    >
                    <ButtonLink 
                        styling="primary"
                        size="large"
                        @click="showAlert"
                        >Large button, no href, v-on</ButtonLink
                    >
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="flex-grow"
                        >This is a default button as &lt;a&gt; link:
                    </span>
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="default"
                        size="small"
                        >Small button</ButtonLink
                    >
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="default"
                        size="default"
                        >Default button</ButtonLink
                    >
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="default"
                        size="medium"
                        >Medium button</ButtonLink
                    >
                    <ButtonLink styling="default" size="large" @click="showAlert"
                        >Large button, no href, v-on</ButtonLink
                    >
                </div>
                <div class="flex flex-wrap items-end gap-4">
                    <span class="flex-grow"
                        >This is a secondary button as &lt;a&gt; link:
                    </span>
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="secondary"
                        size="small"
                        >Small button</ButtonLink
                    >
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="secondary"
                        size="default"
                        >Default button</ButtonLink
                    >
                    <ButtonLink
                        :href="route('partner.dashboard')"
                        styling="secondary"
                        size="medium"
                        >Medium button</ButtonLink
                    >
                    <ButtonLink styling="secondary" size="large" @click="showAlert"
                        >Large button, no href, v-on</ButtonLink
                    >
                </div>
            </div>

            <ButtonLink styling="primary" size="default" type="button" @click="showModal = true">
                Modal Dialog
            </ButtonLink>
            <DialogModal :show="showModal" @close="showModal = false">
                <template #title>
                    Modal title
                </template>

                <template #content>
                    content
                </template>

                <template #footer>
                    <ButtonLink @click="closeModal">Cancel</ButtonLink>
                    <ButtonLink class="ml-3" @click="closeModal">Confirm</ButtonLink>
                </template>
            </DialogModal>
        </div>
    </Section>

    <Section class="bg-white p-2 mb-4">
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

    <Section class="bg-white p-2 mb-4">
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

    <Section class="bg-white p-2 mb-4">
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

    <Section class="bg-white p-2 mb-4">
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

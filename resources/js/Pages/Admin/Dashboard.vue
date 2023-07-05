<script setup>
import Section from "@/Components/Section.vue";
import CardBasic from "@/Components/CardBasic.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
</script>

<template>
    <Section bg="bg-transparent">
        <div class="flex flex-wrap gap-6">
            <CardBasic width="xs">
                <template #header> Partners </template>

                <template #default>
                    We have {{ $page.props.partners_count }} partners in the
                    system
                </template>

                <template #footer>
                    <ButtonLink
                        styling="secondary"
                        size="default"
                        :href="route('admin.partners.index')"
                        type="primary"
                        >Manage</ButtonLink
                    >
                </template>
            </CardBasic>
            <CardBasic width="xs">
                <template #header> Partners subscribed </template>

                <template #default>
                    <div class="text-3xl">
                        {{ $page.props.active_subscriptions_count }}
                    </div>
                </template>
            </CardBasic>
            <CardBasic width="xs">
                <template #header> Instances running </template>

                <template #default>
                    <div class="text-3xl">1</div>
                </template>
                <template #footer>
                    <ButtonLink
                        styling="secondary"
                        size="default"
                        :href="route('admin.instances.index')"
                        type="primary"
                        >Manage</ButtonLink
                    >
                </template>
            </CardBasic>
        </div>
    </Section>
</template>

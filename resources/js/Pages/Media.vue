<script setup>
import {computed, ref} from "vue";
import {Head} from '@inertiajs/inertia-vue3';
import useMedia from "@/Composables/useMedia";
import PageHeader from '@/Components/DataDisplay/PageHeader.vue';
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import MediaUploads from "@/Components/Media/MediaUploads.vue";
import MediaStock from "@/Components/Media/MediaStock.vue";
import MediaGifs from "@/Components/Media/MediaGifs.vue";
import SelectableBar from "@/Components/DataDisplay/SelectableBar.vue";
import PureDangerButton from "@/Components/Button/PureDangerButton.vue";
import TrashIcon from "@/Icons/Trash.vue";
import PlusIcon from "@/Icons/Plus.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import Panel from "@/Components/Surface/Panel.vue";

const {
    activeTab,
    tabs
} = useMedia();

const sources = {
    'uploads': MediaUploads,
    'stock': MediaStock,
    'gifs': MediaGifs
};

const sourceProperties = ref();

const source = computed(() => {
    return sources[activeTab.value]
})

const selectedItems = computed(() => {
    return sourceProperties.value ? sourceProperties.value.selected : [];
})

const unselectAll = () => {
    sourceProperties.value.unselectAll()
}
</script>
<template>
    <Head title="Media Library"/>

    <div class="row-py mb-2xl">
        <PageHeader title="Media Library"/>

        <div class="w-full row-px">
            <Tabs>
                <template v-for="(tabName, tabKey) in tabs" :key="tabKey">
                    <Tab @click="activeTab = tabKey" :active="activeTab === tabKey">{{ tabName }}</Tab>
                </template>
            </Tabs>
        </div>

        <div class="w-full row-px mt-lg">
            <Panel>
                <component :is="source" ref="sourceProperties" :columns="4"/>

                <SelectableBar :count="selectedItems.length" @close="unselectAll()">
                    <SecondaryButton class="mr-sm" size="xs">
                        <PlusIcon class="mr-xs"/>
                        Use
                    </SecondaryButton>
                    <PureDangerButton v-tooltip="'Delete'">
                        <TrashIcon/>
                    </PureDangerButton>
                </SelectableBar>
            </Panel>
        </div>
    </div>
</template>

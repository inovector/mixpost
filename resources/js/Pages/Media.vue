<script setup>
import {computed, ref} from "vue";
import {Head} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import usePostVersions from "@/Composables/usePostVersions";
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
    tabs,
    isDownloading,
    downloadExternal,
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

const use = () => {
    const toDownload = activeTab.value !== 'uploads';

    if (toDownload) {
        downloadExternal(selectedItems.value, (response) => {
            createPost(response.data);
        });
    }

    if (!toDownload) {
        createPost(selectedItems.value);
    }
}

const {versionContentObject} = usePostVersions();

const createPost = (media) => {
    Inertia.post(route('mixpost.posts.store'), {
        versions: [
            {
                account_id: 0,
                is_original: true,
                content: [
                    versionContentObject('', media.map((item) => item.id))
                ]
            }
        ]
    });
}
</script>
<template>
    <Head title="Media Library"/>

    <div class="max-w-5xl w-full mx-auto row-py mb-2xl">
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
                    <SecondaryButton @click="use" :isLoading="isDownloading" :disabled="isDownloading" class="mr-sm"
                                     size="xs">
                        <PlusIcon class="mr-xs"/>
                        Create Post
                    </SecondaryButton>

                    <PureDangerButton v-if="activeTab === 'uploads'" v-tooltip="'Delete'">
                        <TrashIcon/>
                    </PureDangerButton>
                </SelectableBar>
            </Panel>
        </div>
    </div>
</template>

<script setup>
import {computed, ref} from "vue";
import {Head} from '@inertiajs/vue3';
import {router} from "@inertiajs/vue3";
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
import DangerButton from "@/Components/Button/DangerButton.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import Panel from "@/Components/Surface/Panel.vue";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import TrashIcon from "@/Icons/Trash.vue";
import PlusIcon from "@/Icons/Plus.vue";

const {
    activeTab,
    tabs,
    isDownloading,
    isDeleting,
    downloadExternal,
    deletePermanently,
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

const deselectAll = () => {
    sourceProperties.value.deselectAll()
}

const use = () => {
    const toDownload = activeTab.value !== 'uploads';

    if (toDownload) {
        downloadExternal(selectedItems.value.map((item) => {
            const {id, url, download_data} = item;
            return {id, url, download_data};
        }), (response) => {
            createPost(response.data);
        });
    }

    if (!toDownload) {
        createPost(selectedItems.value);
    }
}

const {versionContentObject} = usePostVersions();

const createPost = (media) => {
    router.post(route('mixpost.posts.store'), {
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

const confirmationDeletion = ref(false);

const deleteSelectedItems = () => {
    const items = selectedItems.value.map((item) => item.id);

    deletePermanently(items, () => {
        deselectAll();
        sourceProperties.value.removeItems(items);
        confirmationDeletion.value = false;
    })
}
</script>
<template>
    <Head title="Media Library"/>

    <div class="w-full mx-auto row-py">
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

                <SelectableBar :count="selectedItems.length" @close="deselectAll()">
                    <SecondaryButton @click="use" :isLoading="isDownloading" :disabled="isDownloading" class="mr-sm"
                                     size="xs">
                        <PlusIcon class="mr-xs"/>
                        Create Post
                    </SecondaryButton>

                    <template v-if="activeTab === 'uploads'">
                        <PureDangerButton @click="confirmationDeletion = true" v-tooltip="'Delete'">
                            <TrashIcon/>
                        </PureDangerButton>
                    </template>
                </SelectableBar>
            </Panel>
        </div>
    </div>

    <ConfirmationModal :show="confirmationDeletion" variant="danger" @close="confirmationDeletion = false">
        <template #header>
            Delete media
        </template>
        <template #body>
            Are you sure you want to delete selected media items?
        </template>
        <template #footer>
            <SecondaryButton @click="confirmationDeletion = false" class="mr-xs">Cancel</SecondaryButton>
            <DangerButton :isLoading="isDeleting" :disabled="isDeleting" @click="deleteSelectedItems">Delete</DangerButton>
        </template>
    </ConfirmationModal>
</template>

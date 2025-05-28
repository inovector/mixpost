<script setup>
import {computed, ref} from "vue";
import useMedia from "@/Composables/useMedia";
import useNotifications from "@/Composables/useNotifications";
import DialogModal from "@/Components/Modal/DialogModal.vue"
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import MediaUploads from "@/Components/Media/MediaUploads.vue";
import MediaStock from "@/Components/Media/MediaStock.vue";
import MediaGifs from "@/Components/Media/MediaGifs.vue";
import Preloader from "@/Components/Util/Preloader.vue"

import XIcon from "@/Icons/X.vue"

const props = defineProps({
    maxSelection: {
        type: Number,
        default: 1,
    },
    combinesMimeTypes: {
        type: String,
        default: '',
    }
})

const emit = defineEmits(['insert']);

const {notify} = useNotifications();

const show = ref(false);

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

const deselectAll = () => {
    sourceProperties.value.deselectAll()
}

const close = () => {
    deselectAll();
    show.value = false;
    activeTab.value = 'uploads'
};

const insert = () => {
    const toDownload = activeTab.value !== 'uploads';

    if (toDownload) {
        // Download external media files
        downloadExternal(selectedItems.value.map((item) => {
            const {id, url, download_data} = item;
            return {id, url, download_data};
        }), (response) => {
            emit('insert', response.data);
            close();
        })
    }

    if (!toDownload) {
        emit('insert', selectedItems.value);
        close();
    }
}
</script>
<template>
    <div @click="show = !show">
        <slot/>
    </div>

    <DialogModal :show="show"
                 max-width="2xl"
                 :closeable="true"
                 :scrollable-body="true"
                 @close="close">
        <template #header>
            Add Media
        </template>

        <template #body>
            <Preloader v-if="isDownloading" :opacity="75">
                Downloading...
            </Preloader>

            <Tabs>
                <template v-for="(tabName, tabId) in tabs">
                    <Tab @click="activeTab = tabId" :active="activeTab === tabId">{{ tabName }}</Tab>
                </template>
            </Tabs>

            <div class="mt-lg">
                <component :is="source" ref="sourceProperties"/>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">Cancel</SecondaryButton>

            <template v-if="selectedItems.length">
                <SecondaryButton @click="deselectAll" v-tooltip.top="'Dismiss'" class="mr-xs">
                    <XIcon class="w-5! h-5!"/>
                </SecondaryButton>

                <PrimaryButton @click="insert">Insert {{ selectedItems.length }} items
                </PrimaryButton>
            </template>
        </template>
    </DialogModal>
</template>

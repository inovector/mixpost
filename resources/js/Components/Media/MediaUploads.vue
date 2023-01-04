<script setup>
import {onMounted} from "vue";
import useMedia from "@/Composables/useMedia";
import useNotifications from "@/Composables/useNotifications";
import UploadMedia from "@/Components/Media/UploadMedia.vue"
import MediaSelectable from "@/Components/Media/MediaSelectable.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Masonry from "@/Components/Layout/Masonry.vue";
import SectionTitle from "@/Components/DataDisplay/SectionTitle.vue";

const props = defineProps({
    columns: {
        type: Number,
        default: 3
    }
})

const {notify} = useNotifications();

const {
    page,
    items,
    endlessPagination,
    selected,
    toggleSelect,
    deselectAll,
    removeItems,
    isSelected,
    createObserver
} = useMedia();

onMounted(() => {
    createObserver();
});

defineExpose({selected, deselectAll, removeItems})
</script>
<template>
    <UploadMedia :max-selection="4"
                 :combines-mime-types="''"
                 :selected="selected"
                 :toggleSelect="toggleSelect"
                 :isSelected="isSelected"
                 :columns="columns"
    />

    <div :class="{'mt-lg': items.length}">
        <template v-if="items.length">
            <SectionTitle class="mb-4">Library</SectionTitle>

            <Masonry :items="items" :columns="columns">
                <template #default="{item}">
                    <MediaSelectable v-if="item" :active="isSelected(item)" @click="toggleSelect(item)">
                        <MediaFile :media="item"/>
                    </MediaSelectable>
                </template>
            </Masonry>
        </template>
        <div ref="endlessPagination" class="-z-10 w-full"/>
    </div>
</template>

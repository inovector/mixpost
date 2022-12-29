<script setup>
import {onMounted, watch} from "vue";
import useMedia from "@/Composables/useMedia";
import MediaSelectable from "@/Components/Media/MediaSelectable.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Masonry from "@/Components/Layout/Masonry.vue";
import SearchInput from "@/Components/Util/SearchInput.vue";
import MediaCredit from "@/Components/Media/MediaCredit.vue";
import NoResult from "@/Components/Util/NoResult.vue";

const props = defineProps({
    columns: {
        type: Number,
        default: 3
    }
})

const emit = defineEmits(['select'])

const {
    isLoaded,
    keyword,
    page,
    items,
    endlessPagination,
    selected,
    toggleSelect,
    unselectAll,
    isSelected,
    createObserver
} = useMedia('mixpost.media.fetchGifs');

onMounted(() => {
    createObserver();
});

watch(selected.value, () => {
    emit('select', selected.value)
})
</script>
<template>
    <slot v-bind:selected="selected" v-bind:unselectAll="unselectAll"/>

    <SearchInput v-model="keyword" placeholder="Search Tenor GIFs"/>

    <div v-if="items.length" class="mt-lg">
        <Masonry :items="items" :columns="columns">
            <template #default="{item}">
                <MediaSelectable v-if="item" :active="isSelected(item)" @click="toggleSelect(item)">
                    <MediaFile :media="item" :key="item.id" class="group">
                        <MediaCredit>
                            GIF from Tenor
                        </MediaCredit>
                    </MediaFile>
                </MediaSelectable>
            </template>
        </Masonry>
    </div>

    <NoResult v-if="isLoaded && !items.length" class="mt-lg">No GIFs found.</NoResult>

    <div ref="endlessPagination" class="-z-10 w-full"/>
</template>

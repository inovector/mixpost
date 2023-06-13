<script setup>
import {computed, onMounted} from "vue";
import {usePage, Link} from "@inertiajs/vue3";
import useMedia from "@/Composables/useMedia";
import MediaSelectable from "@/Components/Media/MediaSelectable.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Masonry from "@/Components/Layout/Masonry.vue";
import SearchInput from "@/Components/Util/SearchInput.vue";
import MediaCredit from "@/Components/Media/MediaCredit.vue";
import NoResult from "@/Components/Util/NoResult.vue";
import Alert from "@/Components/Util/Alert.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";

const props = defineProps({
    columns: {
        type: Number,
        default: 3
    }
})

const enabled = computed(() => {
    return usePage().props.is_configured_service.tenor;
})

const {
    isLoaded,
    keyword,
    page,
    items,
    endlessPagination,
    selected,
    toggleSelect,
    deselectAll,
    isSelected,
    createObserver
} = useMedia('mixpost.media.fetchGifs');

onMounted(() => {
    if (enabled.value) {
        createObserver();
    }
});

defineExpose({selected, deselectAll})
</script>
<template>
    <div v-if="enabled">
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
    </div>

    <template v-if="!enabled">
        <Alert variant="warning" :closeable="false">
            You have not configured Tenor service.
        </Alert>

        <Link :href="route('mixpost.services.index')" class="block mt-md">
            <PrimaryButton>Click to configure</PrimaryButton>
        </Link>
    </template>
</template>

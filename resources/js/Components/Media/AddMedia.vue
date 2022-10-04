<script setup>
import {computed, nextTick, ref, watch} from "vue";
import NProgress from 'nprogress'
import DialogModal from "@/Components/Modal/DialogModal.vue"
import UploadMedia from "@/Components/Media/UploadMedia.vue"
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import SectionTitle from "@/Components/DataDisplay/SectionTitle.vue";
import MediaSelectable from "@/Components/Media/MediaSelectable.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Masonry from "@/Components/Layout/Masonry.vue";

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

const show = ref(false);

const activeTab = ref('upload');

const tabs = computed(() => {
    return {
        'upload': 'Upload',
        'stock': 'Stock',
        'gifs': 'GIFs'
    };
})

const page = ref(1);
const items = ref([]);
const endlessPagination = ref(null);

watch(show, () => {
    if (!show.value) {
        return;
    }

    createObserver();
})

const fetchItems = () => {
    if (!page.value) {
        return;
    }

    NProgress.start();

    axios.get(route('mixpost.media.fetch'), {
        params: {
            page: page.value
        }
    }).then(function (response) {
        const nextLink = response.data.links.next;

        if (nextLink) {
            page.value = response.data.links.next.split('?page=')[1];
        }

        if (!nextLink) {
            page.value = 0;
        }

        items.value = [...items.value, ...response.data.data];
    }).catch(function (error) {
        console.log(error);
    }).finally(()=> {
        NProgress.done();
    });
}

const createObserver = () => {
    const observer = new IntersectionObserver((entries) => {
        const isIntersecting = entries[0].isIntersecting;

        if (isIntersecting) {
            fetchItems();
        }
    });

    nextTick(() => {
        observer.observe(endlessPagination.value);
    });
}

const selected = ref([]);

const toggleSelect = (media) => {
    const index = selected.value.findIndex(item => item.id === media.id);

    if (index < 0 && !media.hasOwnProperty('error')) {
        selected.value.push(media);
    }

    if (index >= 0) {
        selected.value.splice(index, 1);
    }
}

const isSelected = (media) => {
    const index = selected.value.findIndex(item => item.id === media.id);

    return index !== -1;
}

const close = () => {
    selected.value = [];
    show.value = false;
    page.value = 1;
    items.value = [];
};

const insert = () => {
    emit('insert', selected.value);
    close();
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
            <Tabs>
                <template v-for="(tabName, tabId) in tabs">
                    <Tab @click="activeTab = tabId" :active="activeTab === tabId">{{ tabName }}</Tab>
                </template>
            </Tabs>

            <div class="mt-8">
                <UploadMedia :max-selection="maxSelection"
                             :combines-mime-types="combinesMimeTypes"
                             :selected="selected"
                             :toggleSelect="toggleSelect"
                             :isSelected="isSelected"
                />
            </div>

            <div class="mt-8">
                <SectionTitle class="mb-4">Library</SectionTitle>

                <Masonry :items="items" v-if="show">
                    <template #default="{item}">
                        <MediaSelectable :active="isSelected(item)" @click="toggleSelect(item)">
                            <MediaFile :media="item"/>
                        </MediaSelectable>
                    </template>
                </Masonry>
                <div ref="endlessPagination" class="-z-10 w-full -mt-20"/>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-2">Cancel</SecondaryButton>
            <PrimaryButton v-if="selected.length" @click="insert">Insert {{ selected.length }} files
            </PrimaryButton>
        </template>
    </DialogModal>
</template>

<script setup>
import {computed, ref} from "vue";
import MixpostDialogModal from "@/Components/DialogModal.vue"
import MixpostUploadMedia from "@/Components/Media/UploadMedia.vue"
import MixpostTabs from "@/Components/Tabs/Tabs.vue"
import MixpostTab from "@/Components/Tabs/Tab.vue"
import MixpostPrimaryButton from "@/Components/PrimaryButton.vue";
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"

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

const show = ref(true);

const activeTab = ref('upload');

const tabs = computed(() => {
    return {
        'upload': 'Upload',
        'stock': 'Stock',
        'gifs': 'GIFs'
    };
})

const selected = ref([]);

const setSelected = (items) => {
    selected.value = items;
}

const close = () => {
    setSelected([]);
    show.value = false;
};

const insert = () => {
    emit('insert', selected);
}
</script>
<template>
    <div @click="show = !show">
        <slot/>
    </div>

    <MixpostDialogModal :show="show"
                        max-width="2xl"
                        :closeable="true"
                        :scrollable-body="true"
                        @close="close">
        <template #header>
            Add Media
        </template>

        <template #body>
            <MixpostTabs>
                <template v-for="(tabName, tabId) in tabs">
                    <MixpostTab @click="activeTab = tabId" :active="activeTab === tabId">{{ tabName }}</MixpostTab>
                </template>
            </MixpostTabs>

            <div class="mt-8">
                <MixpostUploadMedia :max-selection="maxSelection"
                                    :combines-mime-types="combinesMimeTypes"
                                    @update-select="setSelected"/>
            </div>
        </template>

        <template #footer>
            <MixpostSecondaryButton @click="close" class="mr-2">Cancel</MixpostSecondaryButton>
            <MixpostPrimaryButton v-if="selected.length" @click="insert">Insert {{ selected.length }} files
            </MixpostPrimaryButton>
        </template>
    </MixpostDialogModal>
</template>

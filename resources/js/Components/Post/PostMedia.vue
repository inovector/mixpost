<script setup>
import {ref} from "vue";
import {startsWith} from "lodash";
import Draggable from 'vuedraggable'
import usePost from "@/Composables/usePost";
import DialogModal from "@/Components/Modal/DialogModal.vue"
import MediaFile from "@/Components/Media/MediaFile.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import DangerButton from "@/Components/Button/DangerButton.vue";

const props = defineProps({
    media: {
        type: Array,
        required: true
    }
})

const {editAllowed} = usePost();

const items = ref([]);
const showView = ref(false);
const openedItem = ref({});

const isVideo = (mime_type) => {
    return startsWith(mime_type, 'video')
}

const open = (item) => {
    openedItem.value = item;
    showView.value = true;
}

const close = () => {
    showView.value = false;
    openedItem.value = {};
}

const remove = (id) => {
    const index = props.media.findIndex(item => item.id === id);
    props.media.splice(index, 1);
    close();
}
</script>
<template>
    <div class="mt-lg">
        <Draggable
            :list="media"
            :disabled="!editAllowed"
            v-bind="{
                animation: 200,
                group: 'media',
            }"
            item-key="id"
            class="flex flex-wrap gap-xs"
        >
            <template #item="{element}">
                <div role="button" class="cursor-pointer" @click="open(element)">
                    <MediaFile :media="element" img-height="sm" :imgWidthFull="false" :showCaption="false"/>
                </div>
            </template>
        </Draggable>
    </div>

    <DialogModal :show="showView" @close="close">
        <template #header>
            View Media
        </template>

        <template #body>
            <figcaption class="mb-xs text-sm">{{ openedItem.name }}</figcaption>

            <video v-if="isVideo(openedItem.mime_type)" class="w-auto h-full" controls>
                <source :src="openedItem.url" :type="openedItem.mime_type">
                Your browser does not support the video tag.
            </video>

            <img v-else :src="openedItem.url" alt="Image"/>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">Close</SecondaryButton>
            <DangerButton v-if="editAllowed" @click="remove(openedItem.id)">Remove</DangerButton>
        </template>
    </DialogModal>
</template>

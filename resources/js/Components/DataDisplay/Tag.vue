<script setup>
import {nextTick, ref} from "vue";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import {lightOrDark} from "@/helpers";
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue"
import ColorPicker from "@/Components/Package/ColorPicker.vue";
import DialogModal from "@/Components/Modal/DialogModal.vue";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import DangerButton from "@/Components/Button/DangerButton.vue"
import Preloader from "@/Components/Util/Preloader.vue";
import EllipsisHorizontalIcon from "@/Icons/EllipsisHorizontal.vue"
import XIcon from "@/Icons/X.vue"
import PencilSquareIcon from "@/Icons/PencilSquare.vue";
import SwatchIcon from "@/Icons/Swatch.vue";
import TrashIcon from "@/Icons/Trash.vue";

defineEmits(['remove']);

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    removable: {
        type: Boolean,
        default: true
    },
    editable: {
        type: Boolean,
        default: true
    }
})

const {notify} = useNotifications();

const badgeClass = 'min-w-[48px] px-2 rounded-md';
const colorLight = '#0f172a';
const colorDark = '#f8fafc';

// Rename
const rename = ref(false);
const renameRef = ref(null);
const renameText = ref('');
const isRenaming = ref(false);

const openRename = () => {
    rename.value = true;
    renameText.value = props.item.name;

    nextTick(() => {
        renameRef.value.focus();
    })
}

const closeRename = () => {
    rename.value = false;
    renameText.value = ''
}

const renameTag = () => {
    if (props.item.name === renameText.value) {
        closeRename();
        return;
    }

    router.put(route('mixpost.tags.update', {tag: props.item.uuid}), {
        action: 'name',
        name: renameText.value
    }, {
        onStart() {
            isRenaming.value = true
        },
        onFinish() {
            isRenaming.value = false;
            closeRename();
        }
    })
}

// Change color
const changeColorModal = ref(false);
const changeColorHex = ref('');
const isColorChanging = ref(false);

const openChangeColorModal = () => {
    changeColorModal.value = true;
    changeColorHex.value = props.item.hex_color;
}

const closeChangeColorModal = () => {
    changeColorModal.value = false;
    changeColorHex.value = '';
}

const changeTagColor = () => {
    router.put(route('mixpost.tags.update', {tag: props.item.uuid}), {
        action: 'color',
        hex_color: changeColorHex.value,
    }, {
        onStart() {
            isColorChanging.value = true
        },
        onFinish() {
            isColorChanging.value = false;
            closeChangeColorModal();
        }
    })
}

// Delete
const confirmationTagDeletion = ref(false);
const isDeleting = ref(false);

const deleteTag = () => {
    router.delete(route('mixpost.tags.delete', {tag: props.item.uuid}), {
        onStart() {
            isDeleting.value = true;
        },
        onSuccess() {
            confirmationTagDeletion.value = false;
            notify('success', 'Label deleted');
        },
        onFinish() {
            isDeleting.value = false;
        },
    })
}
</script>
<template>
<div>
    <div
        :style="{backgroundColor: item.hex_color, color: lightOrDark(item.hex_color) === 'light' ? colorLight : colorDark}"
        :class="badgeClass"
        class="relative group">
        <Preloader v-if="isRenaming" size="sm" :opacity="50"/>

        <div
            v-if="removable || editable"
            :style="{backgroundColor: item.hex_color}"
            class="tag-actions absolute right-0 top-0 h-full pl-0.5 hidden items-center rounded-r-md group-hover:flex">
            <template v-if="editable">
                <Dropdown width-classes="w-48">
                    <template #trigger>
                        <div tabindex="0" role="button" class="group-btn">
                            <EllipsisHorizontalIcon class="w-5! h-5! opacity-75 [.group-btn:hover_&]:opacity-100"/>
                        </div>
                    </template>

                    <template #content>
                        <DropdownItem @click="openRename" as="button">
                            <PencilSquareIcon class="w-5! h-5! mr-1"/>
                            Rename
                        </DropdownItem>

                        <DropdownItem @click="openChangeColorModal" as="button">
                            <SwatchIcon class="w-5! h-5! mr-1"/>
                            Change color
                        </DropdownItem>

                        <DropdownItem @click="confirmationTagDeletion = true" as="button">
                            <TrashIcon class="w-5! h-5! mr-1 text-red-500"/>
                            Delete
                        </DropdownItem>
                    </template>
                </Dropdown>
            </template>

            <div v-if="removable" @click="$emit('remove')" tabindex="0" role="button" class="ml-1 group-btn">
                <XIcon class="w-5! h-5! opacity-75 [.group-btn:hover_&]:opacity-100"/>
            </div>
        </div>

        <div v-if="!rename">
            {{ item.name }}
        </div>

        <div v-if="rename" class="relative">
            <input ref="renameRef"
                   v-model="renameText"
                   @keyup.enter="renameTag"
                   @blur="closeRename"
                   class="p-0 w-auto outline-hidden focus:outline-0 focus:outline-hidden border-none bg-transparent rounded-md"/>
        </div>
    </div>

    <template v-if="editable">
        <DialogModal :show="changeColorModal" max-width="md" @close="closeChangeColorModal">
            <template #header>
                Change label color
            </template>
            <template #body>
                <template v-if="changeColorModal" class="flex flex-col">
                    <div
                        :style="{backgroundColor: changeColorHex, color: lightOrDark(changeColorHex) === 'light' ? colorLight : colorDark}"
                        :class="badgeClass" class="w-fit">{{ item.name }}
                    </div>
                    <div class="mt-4">
                        <ColorPicker v-model="changeColorHex"/>
                    </div>
                </template>
            </template>
            <template #footer>
                <SecondaryButton @click="closeChangeColorModal" :disabled="isColorChanging" class="mr-xs">Cancel
                </SecondaryButton>
                <PrimaryButton @click="changeTagColor" :is-loading="isColorChanging"
                               :disabled="isColorChanging">Save changes
                </PrimaryButton>
            </template>
        </DialogModal>
        <ConfirmationModal :show="confirmationTagDeletion" variant="danger" @close="confirmationTagDeletion = false">
            <template #header>
                Delete label
            </template>
            <template #body>
                Are you sure you want to delete the {{ item.name }} label from everywhere?
            </template>
            <template #footer>
                <SecondaryButton @click="confirmationTagDeletion = false" :disabled="isDeleting" class="mr-xs">Cancel
                </SecondaryButton>
                <DangerButton @click="deleteTag" :is-loading="isDeleting"
                              :disabled="isDeleting">Delete
                </DangerButton>
            </template>
        </ConfirmationModal>
    </template>
</div>
</template>

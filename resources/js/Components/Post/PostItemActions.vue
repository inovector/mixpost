<script setup>
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import emitter from "@/Services/emitter";
import useNotifications from "@/Composables/useNotifications";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import PureButtonLink from "@/Components/Button/PureButtonLink.vue";
import PureButton from "@/Components/Button/PureButton.vue";
import EllipsisVerticalIcon from "@/Icons/EllipsisVertical.vue"
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import DangerButton from "@/Components/Button/DangerButton.vue"
import PencilSquareIcon from "@/Icons/PencilSquare.vue";
import DuplicateIcon from "@/Icons/Duplicate.vue";
import TrashIcon from "@/Icons/Trash.vue";

const props = defineProps({
    itemId: {
        type: Number,
        required: true,
    }
})

const emit = defineEmits(['onDelete'])

const confirmationDeletion = ref(false);

const {notify} = useNotifications();

const deletePost = () => {
    Inertia.delete(route('mixpost.posts.delete', {post: props.itemId}), {
        onSuccess() {
            confirmationDeletion.value = false;
            notify('success', 'Post deleted')
            emit('onDelete')
            emitter.emit('postDelete', props.itemId);
        }
    })
}

const duplicate = () => {
    Inertia.post(route('mixpost.posts.duplicate', {post: props.itemId}), {}, {
        onSuccess() {
            notify('success', 'Post duplicated')
        }
    })
}
</script>
<template>
    <div>
        <div class="flex flex-row items-center gap-xs">
            <PureButtonLink :href="route('mixpost.posts.edit', {post: itemId})" v-tooltip="'Edit'">
                <PencilSquareIcon/>
            </PureButtonLink>

            <Dropdown width-classes="w-32" placement="bottom-end">
                <template #trigger>
                    <PureButton class="mt-1">
                        <EllipsisVerticalIcon/>
                    </PureButton>
                </template>

                <template #content>
                    <DropdownItem @click="duplicate" as="button">
                        <DuplicateIcon class="!w-5 !h-5 mr-1"/>
                        Duplicate
                    </DropdownItem>

                    <DropdownItem @click="confirmationDeletion = true" as="button">
                        <TrashIcon class="!w-5 !h-5 mr-1 text-red-500"/>
                        Delete
                    </DropdownItem>
                </template>
            </Dropdown>
        </div>

        <ConfirmationModal :show="confirmationDeletion" variant="danger" @close="confirmationDeletion = false">
            <template #header>
                Delete post
            </template>
            <template #body>
                Are you sure you want to delete this post?
            </template>
            <template #footer>
                <SecondaryButton @click="confirmationDeletion = false" class="mr-xs">Cancel</SecondaryButton>
                <DangerButton @click="deletePost">Delete</DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>

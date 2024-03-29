<script setup>
import Modal from '@/Components/Modal/Modal.vue';
import PureButton from "../Button/PureButton.vue";
import X from "../../Icons/X.vue";

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    scrollableBody: {
        type: Boolean,
        default: false,
    }
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        :dialog-class="scrollableBody ? 'overflow-hidden' : ''"
        @close="close"
    >
        <div class="w-full h-full min-h-full max-h-max relative overflow-x-hidden overflow-y-auto">
            <div class="flex flex-col h-full w-full">
                <div v-if="$slots.header" class="flex justify-between px-lg py-md text-lg">
                    <slot name="header"/>

                    <template v-if="closeable">
                        <PureButton @click="close"><X/></PureButton>
                    </template>
                </div>

                <div class="p-lg h-full overflow-x-hidden overflow-y-auto" :class="{'pt-0': $slots.header}">
                    <slot name="body"/>
                </div>

                <div v-if="$slots.footer"
                     class="flex flex-row justify-end px-lg py-md border-t border-gray-200 text-right">
                    <slot name="footer"/>
                </div>
            </div>
        </div>
    </Modal>
</template>

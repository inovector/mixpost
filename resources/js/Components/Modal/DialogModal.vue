<script setup>
import Modal from '@/Components/Modal/Modal.vue';

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
                <div v-if="$slots.header" class="px-6 py-4 text-lg">
                    <slot name="header"/>
                </div>

                <div class="px-6 py-4 h-full overflow-x-hidden overflow-y-auto" :class="{'pt-0': $slots.header}">
                    <slot name="body"/>
                </div>

                <div v-if="$slots.footer"
                     class="flex flex-row justify-end px-6 py-4 border-t border-gray-200 text-right">
                    <slot name="footer"/>
                </div>
            </div>
        </div>
    </Modal>
</template>

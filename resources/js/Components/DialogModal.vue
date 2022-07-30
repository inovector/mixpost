<script setup>
import MixpostModal from '@/Components/Modal.vue';

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
});

const close = () => {
    emit('close');
};
</script>

<template>
    <MixpostModal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-4">
            <div v-if="$slots.header" class="text-lg">
                <slot name="header" />
            </div>

            <div class="mt-4">
                <slot name="body" />
            </div>
        </div>

        <div v-if="$slots.footer" class="flex flex-row justify-end px-6 py-4 border-t border-gray-200 text-right">
            <slot name="footer" />
        </div>
    </MixpostModal>
</template>

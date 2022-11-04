<script setup>
import {computed} from "vue";
import DialogModal from "@/Components/Modal/DialogModal.vue"
import ExclamationIcon from "@/Icons/Exclamation.vue"

const emit = defineEmits(['close']);

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: 'md',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    variant: {
        type: String,
        default: 'warning'
    }
});

const exclamationBgClasses = computed(() => {
    return {
        'warning': 'bg-orange-100',
        'danger': 'bg-red-100',
    }[props.variant];
});

const exclamationIconClasses = computed(() => {
    return {
        'warning': 'text-orange-600',
        'danger': 'text-red-600',
    }[props.variant];
});

const close = () => {
    emit('close');
};
</script>

<template>
    <DialogModal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <template #body>
            <div class="sm:flex sm:items-start">
                <div :class="exclamationBgClasses" class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                   <ExclamationIcon :class="exclamationIconClasses"/>
                </div>

                <div class="flex flex-col text-center sm:mt-0 sm:ml-md mt-md sm:mt-0 sm:text-left">
                    <div class="text-lg"><slot name="header"/></div>
                    <div class="mt-xs"><slot name="body"/></div>
                </div>
            </div>
        </template>

        <template #footer>
            <slot name="footer"/>
        </template>
    </DialogModal>
</template>

<script setup>
import {Dropdown as VDropdown} from "floating-vue";
import '@css/overrideVDropdown.css'

const props = defineProps({
    placement: {
        type: String,
        default: 'bottom',
    },
    widthClasses: {
        type: String,
        default: 'w-48',
    },
    headerPadding: {
        type: String,
        default: 'p-md'
    },
    contentClasses: {
        type: Array,
        default: () => ['py-0', 'bg-white'],
    },
    closeableOnContent: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const closeOnContentClick = (hide) => {
    if (props.closeableOnContent) {
        close(hide);
    }
}

const close = (hide) => {
    if(hide !== undefined) {
        hide();
    }

    emit('close');
}
</script>
<template>
    <VDropdown @apply-hide="close" :placement="placement">
        <slot name="trigger"/>
        <template #popper="{ hide }">
            <div v-if="$slots.header" :class="[headerPadding]" class="border-b border-gray-200">
                <slot name="header"/>
            </div>
            <div @click="closeOnContentClick(hide)" :class="[contentClasses, widthClasses]">
                <slot name="content"/>
            </div>
        </template>
    </VDropdown>
</template>

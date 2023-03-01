<script setup>
import {computed} from "vue";

const props = defineProps({
    value: {
        type: String,
        required: true
    },
    showName: {
        type: Boolean,
        required: false,
        default: true
    }
})

const classNames = computed(() => {
    return {
        'DRAFT': 'bg-gray-500',
        'PUBLISHED': 'bg-lime-500',
        'PUBLISHING': 'bg-violet-500',
        'SCHEDULED': 'bg-cyan-500',
        'FAILED': 'bg-red-500',
    }[props.value]
})

const name = computed(() => {
    return {
        'DRAFT': 'Draft',
        'PUBLISHED': 'Published',
        'PUBLISHING': 'Publishing',
        'SCHEDULED': 'Scheduled',
        'FAILED': 'Failed',
    }[props.value]
})
</script>
<template>
    <div class="flex items-center">
        <div :class="[classNames]" v-tooltip="`${showName ? '' : name}`" class="w-4 h-4 rounded-full"></div>
        <div v-if="showName" class="ml-xs">{{ name }}</div>
    </div>
</template>

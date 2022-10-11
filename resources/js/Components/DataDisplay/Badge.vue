<script setup>
import {computed} from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: 'neutral'
    },
})

const classes = computed(() => {
    return {
        'neutral': 'bg-gray-100 text-gray-800',
        'dark': 'bg-gray-800 text-gray-100',
        'info': 'bg-cyan-100 text-cyan-800',
        'success': 'bg-lime-100 text-lime-600',
        'warning': 'bg-orange-100 text-orange-600',
        'error': 'bg-red-100 text-red-600',
    }[props.variant]
});
</script>
<template>
    <span :class="classes" class="px-2 inline-flex items-center rounded-md">
        <slot/>
    </span>
</template>

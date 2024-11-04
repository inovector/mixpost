<script setup>
const props = defineProps({
    gap: {
        type: String,
        default: 'gap-xs'
    },
    col: {
        type: Boolean,
        default: false,
    },
    wrap: {
        type: Boolean,
        default: false,
    },
    responsive: {
        type: Boolean,
        default: true,
    }
})
</script>
<template>
    <div
        :class="[gap, {'flex-col': col}, {'flex-col md:flex-row': responsive && !col, 'flex-wrap': wrap}]"
        class="flex">
        <slot/>
    </div>
</template>

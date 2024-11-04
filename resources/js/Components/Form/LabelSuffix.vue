<script setup>
defineProps({
    danger: {
        type: Boolean,
        default: false,
    }
})
</script>
<template>
    <span :class="{'text-red-500': danger, 'text-gray-500': !danger}" class="font-normal">
        <slot/>
    </span>
</template>

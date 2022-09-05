<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])
</script>
<template>
    <div @click="$emit('click')" class="relative p-1 cursor-pointer box-border">
        <slot></slot>
    </div>
</template>

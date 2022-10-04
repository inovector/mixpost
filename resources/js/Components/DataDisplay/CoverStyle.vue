<script setup>
defineProps({
    src: {
        type: String,
        required: true
    },
    alt: {
        type: String,
        default: ''
    },
    title: {
        type: String,
        default: ''
    },
})

defineEmits(['load'])
</script>
<template>
    <img :src="src" class="h-full w-full object-cover" :alt="alt" :title="title" @load="$emit('load', $event)"/>
</template>

<script setup>
defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <img :src="media.thumb_url" draggable="false" class="rounded-xl h-full w-full" alt="thumbnail"/>
</template>

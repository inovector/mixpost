<script setup>
defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div class="relative">
        <img :src="media.thumb_url" draggable="false" class="rounded-xl h-full w-full" alt="Image"/>
    </div>
</template>

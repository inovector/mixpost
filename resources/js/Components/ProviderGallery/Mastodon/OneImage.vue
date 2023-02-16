<script setup>
defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div class="h-[308.25px]">
        <div class="w-full h-full inset-0 rounded-lg relative">
            <img :src="media.thumb_url" draggable="false" class="rounded-lg h-full w-full object-cover object-center" alt="Image"/>
        </div>
    </div>
</template>

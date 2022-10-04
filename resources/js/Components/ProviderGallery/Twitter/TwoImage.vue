<script setup>
import Box from "@/Components/Layout/Box.vue";

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <Box>
        <div class="block w-full" style="padding-bottom: 56.25%;"></div>
        <div class="flex flex-row gap-1 absolute top-0 left-0 w-full h-full">
            <div v-for="item in media" :key="media.id" class="flex-grow basis-0 relative w-full h-full basis-auto">
                <div class="absolute flex flex-col top-0 bottom-0 left-0 right-0 overflow-hidden">
                    <div class="bg-cover bg-center h-full w-full"
                         :style="{backgroundImage: 'url('+item.thumb_url+')'}"></div>
                </div>
            </div>
        </div>
    </Box>
</template>

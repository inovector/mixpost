<script setup>
import Box from "@/Components/Layout/Box.vue";
import CoverStyle from "@/Components/DataDisplay/CoverStyle.vue";
import PlayIcon from "@/Icons/Play.vue"

defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <Box>
        <div v-if="media.is_video" class="w-full h-full absolute flex items-center justify-center">
            <div class="w-16 h-16 border-2 border-white rounded-full flex items-center justify-center text-white">
                <PlayIcon class="!w-10 !h-10"/>
            </div>
        </div>
        <CoverStyle :src="media.thumb_url" alt="Image"/>
    </Box>
</template>

<script setup>
import Box from "@/Components/Layout/Box.vue";
import CoverStyle from "@/Components/DataDisplay/CoverStyle.vue";

defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <Box>
        <CoverStyle :src="media.thumb_url" alt="Image"/>
    </Box>
</template>

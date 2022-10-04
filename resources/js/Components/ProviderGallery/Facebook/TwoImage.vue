<script setup>
import AspectRatioBox from "@/Components/Layout/AspectRatioBox.vue";
import Box from "@/Components/Layout/Box.vue";
import CoverStyle from "@/Components/DataDisplay/CoverStyle.vue";

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div class="flex flex-row gap-1">
        <div v-for="item in media" :key="media.id" class="w-1/2 grow-0">
            <Box>
                <AspectRatioBox>
                    <CoverStyle :src="item.thumb_url"/>
                </AspectRatioBox>
            </Box>
        </div>
    </div>
</template>

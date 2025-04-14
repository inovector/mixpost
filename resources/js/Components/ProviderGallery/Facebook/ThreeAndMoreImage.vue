<script setup>
import {ref} from "vue";
import AspectRatioBox from "@/Components/Layout/AspectRatioBox.vue";
import Box from "@/Components/Layout/Box.vue";
import CoverStyle from "@/Components/DataDisplay/CoverStyle.vue";

defineProps({
    media: {
        type: Array,
        required: true
    }
})

const firstImageRef = ref(null);
const firstImageOrientation = ref(null);

const handleImageLoad = () => {
    if(!firstImageRef.value) {
        return;
    }

    const orientation = firstImageRef.value.naturalHeight > firstImageRef.value.naturalWidth ? 'VERTICAL' : 'HORIZONTAL';

    if(firstImageOrientation.value !== orientation) {
        firstImageOrientation.value = orientation;
    }
}
</script>
<template>
    <img ref="firstImageRef" :src="media[0].thumb_url" @load="handleImageLoad" alt="Image" class="hidden"/>

    <template v-if="firstImageOrientation === 'HORIZONTAL' || media.length > 4">
        <div class="flex flex-wrap gap-1">
            <div class="w-full h-full flex grow-0 gap-1">
                <Box v-for="item in media.slice(0, media.length < 5 ? 1 : 2)" :key="item.id">
                    <AspectRatioBox :ratio="media.length < 5 ? null : 1">
                        <CoverStyle :src="item.thumb_url"/>
                    </AspectRatioBox>
                </Box>
            </div>
            <div class="w-full flex grow-0 gap-1">
                <Box v-for="(item, index) in media.slice(media.length < 5 ? 1 : 2, 5)">
                    <AspectRatioBox>
                        <CoverStyle :src="item.thumb_url"/>
                    </AspectRatioBox>
                    <div v-if="index === 2 && media.length > 5" class="absolute top-0 right-0 bottom-0 left-0 flex items-center justify-center bg-black/60">
                        <span class="font-medium text-3xl text-white">+{{ media.length - 5 }}</span>
                    </div>
                </Box>
            </div>
        </div>
    </template>
    <template v-else>
        <AspectRatioBox>
            <div class="flex flex-row gap-1">
                <div class="flex grow-0" :class="{'w-1/2': media.length === 3, 'w-2/3': media.length > 3}">
                    <Box>
                        <CoverStyle :src="media[0].thumb_url"/>
                    </Box>
                </div>
                <div class="block" :class="{'w-1/2': media.length === 3, 'w-1/3': media.length > 3}">
                    <div class="flex flex-wrap gap-1">
                        <Box v-for="item in media.slice(1)">
                            <AspectRatioBox>
                                <CoverStyle :src="item.thumb_url"/>
                            </AspectRatioBox>
                        </Box>
                    </div>
                </div>
            </div>
        </AspectRatioBox>
    </template>
</template>

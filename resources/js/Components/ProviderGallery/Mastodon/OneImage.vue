<script setup>
import PlayIcon from "@/Icons/Play.vue"

defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <div v-if="media.is_video" class="h-[308.25px]">
        <div class="w-full h-full inset-0 rounded-lg bg-black relative">
            <div class="w-full h-full absolute flex items-center justify-center">
                <div class="w-16 h-16 border-2 border-white rounded-full flex items-center justify-center text-white">
                    <PlayIcon class="!w-10 !h-10"/>
                </div>
            </div>

            <img :src="media.thumb_url" draggable="false" class="h-full mx-auto"
                 alt="Image"/>
        </div>
    </div>

    <div v-else class="h-[308.25px]">
        <div class="w-full h-full inset-0 rounded-lg relative">
            <img :src="media.thumb_url" draggable="false" class="rounded-lg h-full w-full object-cover object-center" alt="Image"/>
        </div>
    </div>
</template>

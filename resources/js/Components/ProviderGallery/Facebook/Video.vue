<script setup>
import {ref} from "vue";
import Box from "@/Components/Layout/Box.vue";
import CoverStyle from "@/Components/DataDisplay/CoverStyle.vue";
import PlayIcon from "@/Icons/Play.vue"

const isOpen = ref(false);

defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <Box>
        <div class="w-full h-full absolute flex items-center justify-center">
            <button @click="isOpen = true"
                    v-if="!isOpen"
                    class="w-16 h-16 border-2 border-white rounded-full flex items-center justify-center text-white">
                <PlayIcon class="w-10! h-10!"/>
            </button>
        </div>

        <CoverStyle v-if="!isOpen" :src="media.thumb_url" alt="Image"/>

        <video v-if="isOpen" class="w-auto h-full mx-auto" controls autoplay media="">
            <source :src="media.url" :type="media.mime_type">
            Your browser does not support the video tag.
        </video>
    </Box>
</template>

<script setup>
import Video from "@/Components/ProviderGallery/Facebook/Video.vue"
import OneImage from "@/Components/ProviderGallery/Facebook/OneImage.vue"
import TwoImage from "@/Components/ProviderGallery/Facebook/TwoImage.vue"
import ThreeAndMoreImage from "@/Components/ProviderGallery/Facebook/ThreeAndMoreImage.vue"

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div v-if="media.length">
        <div v-if="media[0].is_video" class="rounded-md overflow-hidden">
            <Video :media="media[0]"/>
        </div>

        <div v-else class="rounded-md overflow-hidden">
            <OneImage v-if="media.length === 1" :media="media[0]"/>
            <TwoImage v-if="media.length === 2" :media="media"/>
            <ThreeAndMoreImage v-if="media.length > 2" :media="media"/>
        </div>
    </div>
</template>

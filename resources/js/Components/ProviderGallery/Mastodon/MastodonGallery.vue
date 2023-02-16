<script setup>
import Video from "@/Components/ProviderGallery/Mastodon/Video.vue"
import OneImage from "@/Components/ProviderGallery/Mastodon/OneImage.vue"
import TwoImage from "@/Components/ProviderGallery/Mastodon/TwoImage.vue"
import ThreeImage from "@/Components/ProviderGallery/Mastodon/ThreeImage.vue"
import FourImage from "@/Components/ProviderGallery/Mastodon/FourImage.vue"

defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div v-if="media.length">
        <div v-if="media[0].is_video" class="overflow-hidden rounded-md">
            <Video :media="media[0]"/>
        </div>

        <div v-else class="overflow-hidden rounded-md">
            <OneImage v-if="media.length === 1" :media="media[0]"/>
            <TwoImage v-if="media.length === 2" :media="media"/>
            <ThreeImage v-if="media.length === 3" :media="media"/>
            <FourImage v-if="media.length > 3" :media="media"/>
        </div>
    </div>
</template>

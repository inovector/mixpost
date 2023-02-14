<script setup>
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
    <div v-if="media.length" class="overflow-hidden rounded-md">
        <OneImage v-if="media.length === 1" :media="media[0]"/>
        <TwoImage v-if="media.length === 2" :media="media"/>
        <ThreeImage v-if="media.length === 3" :media="media"/>
        <FourImage v-if="media.length > 3" :media="media"/>
    </div>
</template>

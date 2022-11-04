<script setup>
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
    <div v-if="media.length" class="rounded-md overflow-hidden">
        <OneImage v-if="media.length === 1" :media="media[0]"/>
        <TwoImage v-if="media.length === 2" :media="media"/>
        <ThreeAndMoreImage v-if="media.length > 2" :media="media"/>
    </div>
</template>

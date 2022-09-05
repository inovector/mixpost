<script setup>
defineProps({
    media: {
        type: Object,
        required: true
    }
})
</script>
<template>
    <figure class="relative bg-indigo-50">
        <div
            class="relative flex rounded"
        >
            <img
                v-if="media.thumb_url"
                :src="media.thumb_url"
                alt="Image"
                class="w-full h-full object-cover rounded"
            />
        </div>
    </figure>
</template>

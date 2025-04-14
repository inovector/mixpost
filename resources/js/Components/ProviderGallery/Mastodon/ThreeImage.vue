<script setup>
defineProps({
    media: {
        type: Array,
        required: true
    }
})
</script>
<template>
    <div class="block relative">
        <div class="block w-full" style="padding-bottom: 56.25%;"></div>
        <div class="flex flex-row gap-1 absolute top-0 left-0 w-full h-full">
            <div class="flex flex-col grow basis-0">
                <div class="grow basis-0 relative w-full h-full basis-auto">
                    <div class="absolute flex flex-col top-0 bottom-0 left-0 right-0 overflow-hidden">
                        <div class="bg-cover bg-center h-full w-full rounded-tl-xl rounded-bl-xl"
                             :style="{backgroundImage: 'url('+media[0].thumb_url+')'}"></div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col grow gap-1 basis-0">
                <div v-for="item in media.slice(1)" :key="item.id" class="grow basis-0 relative w-full h-full basis-auto">
                    <div class="absolute flex flex-col top-0 bottom-0 left-0 right-0 overflow-hidden">
                        <div class="bg-cover bg-center h-full w-full"
                             :style="{backgroundImage: 'url('+item.thumb_url+')'}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

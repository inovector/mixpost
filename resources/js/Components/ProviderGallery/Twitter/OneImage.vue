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
   <div class="relative">
       <div v-if="media.is_video" class="w-full h-full absolute flex items-center justify-center">
           <div class="w-16 h-16 border-2 border-white rounded-full flex items-center justify-center text-white bg-twitter">
               <PlayIcon class="!w-10 !h-10"/>
           </div>
       </div>
       <img :src="media.thumb_url" draggable="false" class="rounded-xl h-full w-full" alt="Image"/>
   </div>
</template>

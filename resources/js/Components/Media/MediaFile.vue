<script setup>
import {computed} from "vue";
import ExclamationCircleIcon from "@/Icons/ExclamationCircle.vue"
import VideoSolidIcon from "@/Icons/VideoSolid.vue"

const props = defineProps({
    media: {
        type: Object,
        required: true
    },
    imgHeight: {
        type: String,
        default: 'full'
    },
    imgWidthFull: {
        type: Boolean,
        default: true
    }
})

const imgHeightClass = computed(() => {
    return {
        'full': 'h-full',
        'sm': 'h-20'
    }[props.imgHeight]
})
</script>
<template>
    <figure class="relative">
        <slot/>
        <div
            class="relative flex rounded"
            :class="{'border border-red-500 p-5': media.hasOwnProperty('error')}"
        >
            <span v-if="media.is_video" class="absolute top-0 right-0 mt-1 mr-1">
                <VideoSolidIcon class="!w-4 !h-4 text-white"/>
            </span>

            <div v-if="media.hasOwnProperty('error')" class="text-center">
                <ExclamationCircleIcon class="w-8 h-8 mx-auto text-red-500"/>
                <div class="mt-xs">{{ media.name }}</div>
                <div class="mt-xs text-red-500">{{
                        media.error ? media.error : $t('media.error_uploading_media')
                    }}
                </div>
            </div>

            <img
                :src="media.thumb_url"
                loading="lazy"
                alt="Image"
                class="rounded-md"
                :class="[imgHeightClass, {'w-full': imgWidthFull}]"
            />
        </div>
    </figure>
</template>

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
    },
    showCaption: {
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
    <figure :class="{'border border-gray-200 rounded-md p-xs bg-stone-500': showCaption}" class="group relative">
        <slot/>
        <div
            class="relative flex rounded-sm"
            :class="{'border border-red-500 p-md': media.hasOwnProperty('error')}"
        >
            <span v-if="media.is_video" class="absolute top-0 left-0 mt-1 ml-1">
                <VideoSolidIcon class="w-4! h-4! text-white"/>
            </span>

            <div v-if="media.hasOwnProperty('error')" class="text-center">
                <ExclamationCircleIcon class="w-8 h-8 mx-auto text-red-500"/>
                <div class="mt-xs">{{ media.name }}</div>
                <div class="mt-xs text-red-500">{{
                        media.error ? media.error : 'Error uploading media.'
                    }}
                </div>
            </div>

            <img
                :src="media.thumb_url"
                :title="media.name"
                alt="Image"
                loading="lazy"
                class="rounded-md"
                :class="[imgHeightClass, {'w-full': imgWidthFull}]"
            />
        </div>
        <template v-if="showCaption">
            <figcaption class="mt-xs text-sm break-all">{{ media.name }}</figcaption>
        </template>
    </figure>
</template>

<script setup>
import useProviderIcon from "@/Composables/useProviderIcon";
import MixpostAlert from "@/Components/Alert.vue";
import MixpostPanel from "@/Components/Panel.vue";
import EditorReadOnly from "@/Components/EditorReadOnly.vue";
const {providerIconComponent} = useProviderIcon('facebook');
import fbIconsImgUrl from "@img/fb-icons.png"

defineProps({
    name: {
        required: true,
        type: String
    },
    username: {
        required: true,
        type: String
    },
    image: {
        required: true,
        type: String
    },
    content: {
        required: true,
        type: Array,
    },
    reachedMaxCharacterLimit: {
        type: Boolean,
        default: false,
    }
})
</script>
<template>
    <MixpostAlert v-if="reachedMaxCharacterLimit" variant="error" :closeable="false" class="mb-2">[{{ name }}] : You have reached the maximum character limit</MixpostAlert>
    <MixpostPanel :class="{'border-red-500': reachedMaxCharacterLimit}" class="relative">
        <div class="absolute right-0 top-0 -mt-3 -mr-2">
            <div class="flex items-center justify-center p-2 w-7 h-7 rounded-full bg-white border border-gray-200">
                <div>
                    <component :is="providerIconComponent" class="text-twitter !w-5 !h-5"/>
                </div>
            </div>
        </div>

        <div class="flex items-center">
            <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full mr-3">
                <img :src="image"
                     class="object-cover w-full h-full rounded-full" alt=""/>
            </span>
            <div class="flex flex-col">
                <div class="font-medium mr-2">{{ name }}</div>
                <div class="text-gray-400 text-sm">19h</div>
            </div>
        </div>

        <div class="w-full">
            <EditorReadOnly :value="$props.content[0].body" class="mt-2"/>
        </div>

        <div class="mt-5 flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex mr-2">
                    <img src="@img/fb-like.svg" class="w-5 h-5 z-10" alt=""/>
                    <img src="@img/fb-wow.svg" class="w-5 h-5 -ml-1" alt=""/>
                </div>
                <div class="text-gray-500">116</div>
            </div>
            <div class="text-gray-500">0 comments</div>
        </div>

        <div class="mt-5 flex items-center justify-around border-t border-b border-gray-200 text-gray-500 py-2">
            <div class="flex items-center">
                <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -342px', backgroundSize: '25px 867px'}" class="facebook-toolbar-icon"></i>
                <span class="ml-1 font-semibold">Like</span>
            </div>
            <div class="flex items-center">
                <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -304px', backgroundSize: '25px 867px'}" class="facebook-toolbar-icon"></i>
                <span class="ml-1 font-semibold">Comment</span>
            </div>
            <div class="flex items-center">
                <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -361px', backgroundSize: '25px 867px'}" class="facebook-toolbar-icon"></i>
                <span class="ml-1 font-semibold">Share</span>
            </div>
        </div>
    </MixpostPanel>
</template>
<style>
    .facebook-toolbar-icon {
        @apply inline-block w-5 h-5 bg-no-repeat;
    }
</style>

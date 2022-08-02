<script setup>
import {computed} from "vue";
import TwitterIcon from "@/Icons/Twitter.vue";
import FacebookIcon from "@/Icons/Facebook.vue";

const props = defineProps({
    imgUrl: {
        type: String,
        required: true,
    },
    provider: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'md'
    }
})

const borderClasses = computed(() => {
    if (!props.active) {
        return 'border-stone-600';
    }

    return 'border-' + props.provider;
});

const activeBgClasses = computed(() => {
    return 'bg-' + props.provider;
});

const sizeImgClasses = computed(() => {
    return {
        'md': 'w-10 h-10',
        'lg': 'w-16 h-16'
    }[props.size];
});

const iconWrapperClasses = computed(() => {
    return {
        'md': 'w-5 h-5 -mb-11 -mr-5',
        'lg': 'w-8 h-8 -mb-16 -mr-9'
    }[props.size];
});

const iconClasses = computed(() => {
    const bySize = {
        'md': '!w-4 !h-4'
    }[props.size];

    return [bySize, 'text-' + props.provider]
});

const providerIcon = computed(() => {
    return {
        'twitter': TwitterIcon,
        'facebook': FacebookIcon,
    }[props.provider];
});
</script>
<template>
    <span :class="{'grayscale': !active}" class="flex items-center justify-center">
        <span :class="borderClasses" class="flex items-center justify-center relative border-2 p-1 rounded-full">
            <span :class="[activeBgClasses, sizeImgClasses]"
                  class="inline-flex justify-center items-center flex-shrink-0 rounded-full">
                <img :src="imgUrl" class="object-cover w-full h-full rounded-full" alt=""/>
            </span>
            <span :class="iconWrapperClasses"
                  class="flex items-center justify-center absolute bg-white p-2 rounded-full ">
                <span>
                    <component :is="providerIcon" :class="iconClasses"/>
                </span>
            </span>
        </span>
    </span>
</template>

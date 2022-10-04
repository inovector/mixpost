<script setup>
import {computed} from "vue";
import useProviderIcon from "@/Composables/useProviderIcon";
import ExclamationCircleIcon from "@/Icons/ExclamationCircle.vue";

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
    },
    warningMessage: {
        type: String,
        default: ''
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
    return {
        'md': '!w-4 !h-4'
    }[props.size];
});

const {providerIconComponent} = useProviderIcon(props.provider);
</script>
<template>
    <span class="flex items-center justify-center">
        <span :class="borderClasses" class="flex items-center justify-center relative border-2 p-1 rounded-full">
            <span :class="[activeBgClasses, sizeImgClasses, {'grayscale': !active}]"
                  class="inline-flex justify-center items-center flex-shrink-0 rounded-full">
                <img :src="imgUrl" class="object-cover w-full h-full rounded-full" alt=""/>
            </span>
            <span v-if="warningMessage" v-tooltip="warningMessage" class="flex items-center justify-center rounded-full absolute top-0 -ml-12 bg-red-500 text-white">
                <ExclamationCircleIcon :class="iconClasses"/>
            </span>
            <span :class="[iconWrapperClasses, {'grayscale': !active}]"
                  class="flex items-center justify-center absolute bg-white p-2 rounded-full ">
                <span>
                    <component :is="providerIconComponent" :class="[iconClasses, ('text-' + props.provider)]"/>
                </span>
            </span>
        </span>
    </span>
</template>

<script setup>
import {computed} from "vue";

const props = defineProps({
    imgUrl: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false
    },
    activeBorderClass: {
        type: String,
        required: true,
    },
    activeBgClass: {
        type: String,
        required: true,
    }
})

const borderClass = computed(()=> {
    return props.active ? props.activeBorderClass : 'border-stone-600';
});
</script>
<template>
    <button :class="{'grayscale': !active}" type="button" class="flex items-center justify-center">
        <span :class="borderClass" class="flex items-center justify-center relative border-2 p-1 rounded-full">
            <span :class="activeBgClass" class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                <img :src="imgUrl" class="object-cover w-full h-full rounded-full" alt=""/>
            </span>
            <span class="flex items-center justify-center absolute bg-white p-2 w-5 h-5 rounded-full -mb-11 -mr-5">
                <span>
                    <slot/>
                </span>
            </span>
        </span>
    </button>
</template>

<script setup>
import useButtonSize from "@/Composables/useButtonSize"
import CircleLoadingIcon from "@/Icons/CircleLoading.vue"

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    size: {
        type: String,
        default: 'lg'
    },
    isLoading: {
        type: Boolean,
        default: false,
    }
});

const {sizeClass} = useButtonSize(props.size);
</script>

<template>
    <button :type="type" :class="sizeClass"
            class="relative inline-flex items-center bg-red-500 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:border-red-700 focus:shadow-outline-red disabled:bg-red-400 disabled:cursor-not-allowed transition ease-in-out duration-200">
        <slot/>
        <span v-if="isLoading" class="absolute left-0 top-0 flex justify-center items-center w-full h-full bg-red-500">
             <CircleLoadingIcon class="animate-spin text-white"/>
        </span>
    </button>
</template>
